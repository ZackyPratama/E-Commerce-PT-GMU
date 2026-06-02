<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Midtrans\Config;
use Midtrans\Snap;

#[Layout('components.layouts.front-end-layout')]
class CheckoutPage extends Component
{
    public $cart = [];
    public $step = 1; // 1: Address, 2: Review, 3: Payment
    // Address fields
    public $useExistingAddress = true;
    public $selectedAddressId = null;
    public $full_name = '';
    public $phone = '';
    public $address_line_1 = '';
    public $address_line_2 = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $country = 'ID';
    // Order details
    public $couponCode = '';
    public $appliedCoupon = null;
    public $paymentMethod = 'midtrans'; // default payment method
    public $customerNotes = '';

    public function mount()
    {
        $this->cart = session()->get('cart', []);

        if (empty($this->cart)) {
            return redirect()->route('cart.index');
        }

        // Pre-fill with customer data
        $customer = auth('customer')->user();
        $this->full_name = $customer->name;
        $this->phone = $customer->phone ?? '';

        // Load default address if exists
        $defaultAddress = $customer->addresses()->where('is_default', true)->first();
        if ($defaultAddress) {
            $this->selectedAddressId = $defaultAddress->id;
        }
    }
    public function selectAddress($addressId)
    {
        $this->selectedAddressId = $addressId;
    }
    public function applyCoupon()
    {
        $coupon = Coupon::where('code', strtoupper($this->couponCode))
            ->valid()
            ->first();

        if (!$coupon) {
            session()->flash('coupon_error', 'Coupon tidak valid atau sudah kadaluarsa');
            return;
        }

        if (!$coupon->canBeUsedByCustomer(auth('customer')->id())) {
            session()->flash('coupon_error', 'Anda telah menggunakan coupon ini sebelumnya.');
            return;
        }

        $this->appliedCoupon = $coupon;
        session()->flash('coupon_success', 'Kupon berhasil diterapkan! Anda mendapatkan diskon ' . $coupon->discount_amount . '%.');
    }

    public function removeCoupon()
    {
        $this->appliedCoupon = null;
        $this->couponCode = '';
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validateAddress();
            $this->step = 2;
        } elseif ($this->step === 2) {
            $this->step = 3;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    // Validasi alamat pengiriman (modif solve error )
    protected function validateAddress()
{
    $customer = auth('customer')->user();
    $hasAddresses = $customer->addresses()->exists();

    if (!$this->useExistingAddress || !$hasAddresses) {
        $this->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:2',
        ]);
    } else {
        $this->validate([
            'selectedAddressId' => [
                'required',
                \Illuminate\Validation\Rule::exists('addresses', 'id')->where(function ($query) use ($customer) {
                    $query->where('customer_id', $customer->id);
                }),
            ],
        ], [
            'selectedAddressId.required' => 'Silakan pilih alamat pengiriman.',
            'selectedAddressId.exists' => 'Alamat pengiriman tidak valid.',
        ]);
    }
}

    public function placeOrder(){
    try {
        DB::beginTransaction();
        // Get shipping address
        if ($this->useExistingAddress && $this->selectedAddressId) {
            $address = Address::find($this->selectedAddressId);
            $shippingData = [
                'shipping_full_name' => $address->full_name,
                'shipping_phone' => $address->phone,
                'shipping_address_line_1' => $address->address_line_1,
                'shipping_address_line_2' => $address->address_line_2,
                'shipping_city' => $address->city,
                'shipping_state' => $address->state,
                'shipping_postal_code' => $address->postal_code,
                'shipping_country' => $address->country,
            ];
        } else {
            $shippingData = [
                'shipping_full_name' => $this->full_name,
                'shipping_phone' => $this->phone,
                'shipping_address_line_1' => $this->address_line_1,
                'shipping_address_line_2' => $this->address_line_2,
                'shipping_city' => $this->city,
                'shipping_state' => $this->state,
                'shipping_postal_code' => $this->postal_code,
                'shipping_country' => $this->country,
            ];
        }

        // Calculate totals
        $subtotal = $this->getSubtotal();
        $shippingCost = $this->getShippingCost();
        $discountAmount = $this->getDiscountAmount();
        $taxAmount = 0; // You can calculate tax here if needed
        $total = $subtotal + $shippingCost + $taxAmount - $discountAmount;

        //create order
        $order = Order::create([
            'customer_id' => auth('customer')->id(),
            'coupon_id' => $this->appliedCoupon?->id,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'shipping_cost' => $shippingCost,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'payment_method' => $this->paymentMethod,
            'payment_status' => 'pending',
            'status' => 'pending',
            'customer_notes' => $this->customerNotes,
        ] + $shippingData);

        // create order items
        foreach ($this->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['variant_id'],
                'product_name' => $item['name'],
                'product_sku' => $item['variant_id'] 
                    ? \App\Models\ProductVariant::find($item['variant_id'])->sku 
                    : \App\Models\Product::find($item['product_id'])->sku,
                'variant_name' => $item['variant_name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        //record the coupon usage
        if ($this->appliedCoupon) {
            $this->appliedCoupon->usages()->create([
                'customer_id' => auth('customer')->id(),
                'order_id' => $order->id,
            ]);
        }

        DB::commit();

        //send order confirmation
        Mail::to($order->customer->email)
        ->queue(new OrderConfirmation($order));

        //proccessing the payment
        if ($this->paymentMethod === 'midtrans') {
            return $this->processMidtransPayment($order);
        } else {
            // Cash on delivery
            session()->forget('cart');
            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Pemesanan berhasil dilakukan!');
        }

        
    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error','Error saat memproses pesanan: '. $e->getMessage());
        return;
    }
}

    public function processMidtransPayment($order)
    {
        try {
            // Set Midtrans config
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$clientKey = config('services.midtrans.client_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // Prepare transaction data
            $transactionDetails = [
                'order_id' => 'ORDER-' . $order->id . '-' . time(),
                'gross_amount' => (int) $order->total, // Jangan dikalikan 100, langsung rupiah
            ];

            $customerDetails = [
                'first_name' => auth('customer')->user()->name,
                'email' => auth('customer')->user()->email,
                'phone' => auth('customer')->user()->phone,
            ];

            // Prepare item details
            $items = [];
            foreach ($order->items as $item) {
                $items[] = [
                    'id' => $item->product_id,
                    'price' => (int) $item->price,
                    'quantity' => $item->quantity,
                    'name' => $item->product_name . ($item->variant_name ? ' - ' . $item->variant_name : ''),
                ];
            }

            // Add shipping
            if ($order->shipping_cost > 0) {
                $items[] = [
                    'id' => 'shipping',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Ongkir',
                ];
            }

            // Menambahkan kalkulasi pajak ke item details Midtrans
            if ($order->tax_amount > 0) {
                $items[] = [
                    'id' => 'tax',
                    'price' => (int) $order->tax_amount,
                    'quantity' => 1,
                    'name' => 'Pajak',
                ];
            }

            // Menambahkan kalkulasi diskon ke item details Midtrans
            if ($order->discount_amount > 0) {
                $items[] = [
                    'id' => 'discount',
                    'price' => -((int) $order->discount_amount), // Harus menggunakan nilai minus
                    'quantity' => 1,
                    'name' => 'Diskon Kupon',
                ];
            }

            // Prepare Midtrans transaction
            $snapTransaction = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $items,
                'callbacks' => [
                    'finish' => route('checkout.success', $order->id),
                    'error' => route('checkout.error', $order->id),
                    'pending' => route('checkout.pending', $order->id),
                ],
            ];

            // Generate Snap Token
            $snapToken = Snap::getSnapToken($snapTransaction);

            // Save snap token to database 
            $order->update([
                'snap_token' => $snapToken,
                'midtrans_order_id' => $transactionDetails['order_id'],
            ]);

            // Redirect to payment page
            return redirect()->route('checkout.payment', $order->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat transaksi: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    protected function getSubtotal()
    {
        return array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $this->cart));
    }

    protected function getShippingCost()
    {
        $subtotal = $this->getSubtotal();
        $freeShippingThreshold = Setting::get('free_shipping_threshold', 100);
        $flatRate = Setting::get('flat_shipping_rate', 10);

        if ($freeShippingThreshold && $subtotal >= $freeShippingThreshold) {
            return 0;
        }

        return $flatRate;
    }
    protected function getDiscountAmount()
    {
        if (!$this->appliedCoupon) {
            return 0;
        }

        return $this->appliedCoupon->calculateDiscount($this->getSubtotal());
    }
    public function render()
    {
        $addresses = auth('customer')->user()->addresses;
        return view('livewire.checkout-page',[
            'addresses' => $addresses,
            'subtotal' => $this->getSubtotal(),
            'shippingCost' => $this->getShippingCost(),
            'discountAmount' => $this->getDiscountAmount(),
            'total' => $this->getSubtotal() + $this->getShippingCost() - $this->getDiscountAmount(),
        ])->layout('livewire.checkout-page', ['title' => 'Checkout']);
    }

}

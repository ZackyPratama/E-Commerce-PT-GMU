<?php

namespace App\Livewire;

use App\Mail\OrderConfirmation;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Midtrans\Config;
use Midtrans\Snap;

#[Layout('components.layouts.front-end-layout', ['title' => 'Checkout'])]
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

    // Load cart data dan data pelanggan saat komponen di-mount
    public function mount()
    {
        $this->cart = session()->get('cart', []);

        if (empty($this->cart)) {
            return redirect()->route('cart.index');
        }

        // Load customer data jika sudah login
        $customer = auth('customer')->user();
        $this->full_name = $customer->name;
        $this->phone = $customer->phone ?? '';

        // load alamat pengiriman default jika ada
        $defaultAddress = $customer->addresses()->where('is_default', true)->first();
        if ($defaultAddress) {
            $this->selectedAddressId = $defaultAddress->id;
        }
    }

    // Fungsi pilih alamat pengiriman
    public function selectAddress($addressId)
    {
        $this->selectedAddressId = $addressId;
    }

    // Fungsi kupon 
    public function applyCoupon()
    {
        $coupon = Coupon::where('code', strtoupper($this->couponCode))
            ->valid()
            ->first();

        if (!$coupon) {
            session()->flash('coupon_error', 'Kupon tidak valid atau sudah kadaluarsa');
            return;
        }

        if (!$coupon->canBeUsedByCustomer(auth('customer')->id())) {
            session()->flash('coupon_error', 'Anda telah menggunakan kupon ini sebelumnya.');
            return;
        }

        $this->appliedCoupon = $coupon;
        session()->flash('coupon_success', 'Kupon berhasil diterapkan! Anda mendapatkan diskon ' . $coupon->discount_amount . '%.');
    }

    // Hapus kupon yang telah dipakai
    public function removeCoupon()
    {
        $this->appliedCoupon = null;
        $this->couponCode = '';
    }

    // alur langkah checkout
    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validateAddress();
            $this->step = 2;
        } elseif ($this->step === 2) {
            $this->step = 3;
        }
    }

    // back ke langkah sebelumnya
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

    // Fungsi untuk memproses pesanan
    public function placeOrder()
    {
        // // Debugging: Melihat semua properti publik yang ada di komponen ini
        // dd($this->all());
        try {
            DB::beginTransaction();
            // ambil data alamat pengiriman yang sudah ada atau data alamat yang diinputkan baru
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
                // Simpan alamat baru jika tidak menggunakan alamat yang sudah ada
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

            // kalkulasi total belanja
            $subtotal = $this->getSubtotal();
            $shippingCost = $this->getShippingCost();
            $discountAmount = $this->getDiscountAmount();
            $taxAmount = 0; // You can calculate tax here if needed
            $total = $subtotal + $shippingCost + $taxAmount - $discountAmount;

            //buat pesanan baru
            $order = Order::create([
                'customer_id' => auth('customer')->id(),
                'coupon_id' => $this->appliedCoupon?->id,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'payment_method' => $this->paymentMethod,
                'payment_status' => PaymentStatusEnum::PENDING,
                'status' => 'pending',
                'customer_notes' => $this->customerNotes,
            ] + $shippingData);

            // buat order item untuk setiap produk di keranjang
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

            // rekam jejak penggunaan kupon jika ada
            if ($this->appliedCoupon) {
                $this->appliedCoupon->usages()->create([
                    'customer_id' => auth('customer')->id(),
                    'order_id' => $order->id,
                ]);
            }

            DB::commit();

            //kirim email konfirmasi pesanan ke pelanggan
            Mail::to($order->customer->email)
                ->queue(new OrderConfirmation($order));

            //proccessing the payment
            if ($this->paymentMethod === 'midtrans') {
                return $this->processMidtransPayment($order);
            } else {
                // Cash on delivery
                session()->forget('cart');
                return redirect()->route('checkout.success', $order->id);
            }


        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saat memproses pesanan: ' . $e->getMessage());
            return;
        }
    }
    public function processMidtransPayment($order)
    {
        try {
            // Configure Midtrans
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$clientKey = config('services.midtrans.client_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized', true);
            Config::$is3ds = config('services.midtrans.is_3ds', true);

            // prepare item details from order items
            $items = [];
            $calculatedGrossAmount = 0;

            foreach ($order->Items as $item) {
                $unitPrice = (int) round($item->price);
                $quantity = (int) $item->quantity;

                $items[] = [
                    'id' => 'item-' . $item->id, // Gunakan ID OrderItem agar unik
                    'price' => $unitPrice,
                    'quantity' => $quantity,
                    'name' => substr($item->product_name, 0, 50), // Limit 50 karakter
                ];
                $calculatedGrossAmount += ($unitPrice * $quantity);
            }

            // Add shipping cost
            if ($order->shipping_cost > 0) {
                $shipping = (int) round($order->shipping_cost);
                $items[] = [
                    'id' => 'SHIPPING',
                    'price' => $shipping,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim',
                ];
                $calculatedGrossAmount += $shipping;
            }

            // Add tax
            if ($order->tax_amount > 0) {
                $tax = (int) round($order->tax_amount);
                $items[] = [
                    'id' => 'TAX',
                    'price' => $tax,
                    'quantity' => 1,
                    'name' => 'Pajak',
                ];
                $calculatedGrossAmount += $tax;
            }

            // Add discount (sebagai item negatif)
            if ($order->discount_amount > 0) {
                $discount = (int) round($order->discount_amount);
                $items[] = [
                    'id' => 'DISCOUNT',
                    'price' => -$discount,
                    'quantity' => 1,
                    'name' => 'Diskon',
                ];
                $calculatedGrossAmount -= $discount;
            }

            // Generate unique Midtrans order ID
            $midtransOrderId = $order->order_number . '-' . time();

            // Prepare transaction details dengan total yang sudah dihitung ulang
            $transactionDetails = [
                'order_id' => $midtransOrderId,
                'gross_amount' => $calculatedGrossAmount,
            ];

            // Prepare customer details
            $customerDetails = [
                'first_name' => $order->customer->name,
                'email' => $order->customer->email,
                'phone' => $order->shipping_phone,
                'billing_address' => [
                    'first_name' => $order->shipping_full_name,
                    'email' => $order->customer->email,
                    'phone' => $order->shipping_phone,
                    'address' => $order->shipping_address_line_1,
                    'city' => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                    'country_code' => strlen($order->shipping_country) === 3 ? $order->shipping_country : 'IDN',
                ],
                'shipping_address' => [
                    'first_name' => $order->shipping_full_name,
                    'email' => $order->customer->email,
                    'phone' => $order->shipping_phone,
                    'address' => $order->shipping_address_line_1,
                    'city' => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                    'country_code' => strlen($order->shipping_country) === 3 ? $order->shipping_country : 'IDN',
                ],
            ];

            $isB2B = $order->customer->isB2BApproved();

            if ($isB2B) {
                $companyName = $order->customer->company_name ?? $order->customer->name;
                $customerDetails['first_name'] = $companyName;
            }

            // Prepare transaction data
            $transactionData = [
                'transaction_details' => $transactionDetails,
                'item_details' => $items,
                'customer_details' => $customerDetails,
                'credit_card' => [
                    'secure' => true,
                ],
            ];

            if ($isB2B) {
                $transactionData['enabled_payments'] = [
                    'bca_va', 'mandiri_va', 'bni_va', 'bri_va', 'permata_va', 'cimb_va', 'other_va',
                ];
                $transactionData['bank_transfer'] = [
                    'va_number' => '',
                    'free_text' => [
                        'enquiry' => [
                            [
                                'title' => 'Nama Perusahaan',
                                'description' => $order->customer->company_name ?? $order->customer->name,
                            ],
                        ],
                    ],
                ];
            }

            // Generate Snap token
            $snapToken = Snap::getSnapToken($transactionData);

            // Store snap token and midtrans order ID in database
            $order->update([
                'total' => $calculatedGrossAmount, // update total agar sesuai dengan yang dikirim ke Midtrans
                'snap_token' => $snapToken,
                'midtrans_order_id' => $midtransOrderId,
            ]);

            // Clear cart from session
            session()->forget('cart');

            // Return redirect to payment page
            return redirect()->route('checkout.payment', $order->id);

        } catch (\Exception $e) {
            \Log::error('Midtrans Snap token generation error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            session()->flash('error', 'Gagal membuat token pembayaran. Silakan coba lagi.');
            return;
        }
    }

    // kalkulasi total belanja 
    protected function getSubtotal()
    {
        return array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $this->cart));
    }

    // kalkulasi ongkir
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

    // kalkulasi diskon
    protected function getDiscountAmount()
    {
        // jika tidak menggunakan kupon
        if (!$this->appliedCoupon) {
            return 0;
        }
        // jika menggunakan kupon. kalkulasi diskon kupon di coupon.php
        return $this->appliedCoupon->calculateDiscount($this->getSubtotal());
    }
    public function render()
    {
        $addresses = auth('customer')->user()->addresses;
        return view('livewire.checkout-page', [
            'addresses' => $addresses,
            'subtotal' => $this->getSubtotal(),
            'shippingCost' => $this->getShippingCost(),
            'discountAmount' => $this->getDiscountAmount(),
            'total' => $this->getSubtotal() + $this->getShippingCost() - $this->getDiscountAmount(),
        ]);

    }

}

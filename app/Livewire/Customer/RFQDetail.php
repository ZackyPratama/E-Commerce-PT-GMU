<?php

namespace App\Livewire\Customer;

use App\Mail\RFQAccepted;
use App\Mail\RFQRejected;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RFQ;
use App\Models\User;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Midtrans\Config;
use Midtrans\Snap;

#[Layout('components.layouts.front-end-layout')]
class RFQDetail extends Component
{
    public RFQ $rfq;

    public function mount($id)
    {
        $customer = auth('customer')->user();
        $this->rfq = RFQ::forCustomer($customer)
            ->with(['items.product.primaryImage', 'items.variant'])
            ->findOrFail($id);
    }

    public function acceptQuotation()
    {
        if (!$this->rfq->isQuoted()) {
            return;
        }

        try {
            DB::beginTransaction();

            $customer = auth('customer')->user();
            $defaultAddress = $customer->addresses()->where('is_default', true)->first();

            $subtotal = $this->rfq->subtotal;
            $shippingCost = 0;
            $total = ($this->rfq->total ?: $subtotal) + $shippingCost;

            $order = Order::create([
                'customer_id' => $customer->id,
                'rfq_id' => $this->rfq->id,
                'subtotal' => $subtotal,
                'discount_amount' => $this->rfq->discount_amount ?? 0,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $this->rfq->tax_amount ?? 0,
                'total' => $total,
                'payment_method' => 'midtrans',
                'payment_status' => PaymentStatusEnum::PENDING,
                'status' => 'pending',
                'customer_notes' => $this->rfq->customer_notes,
                'shipping_full_name' => $defaultAddress?->full_name ?? $customer->name,
                'shipping_phone' => $defaultAddress?->phone ?? $customer->phone,
                'shipping_address_line_1' => $defaultAddress?->address_line_1 ?? '',
                'shipping_address_line_2' => $defaultAddress?->address_line_2,
                'shipping_city' => $defaultAddress?->city ?? '',
                'shipping_state' => $defaultAddress?->state,
                'shipping_postal_code' => $defaultAddress?->postal_code ?? '',
                'shipping_country' => $defaultAddress?->country ?? 'ID',
            ]);

            foreach ($this->rfq->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product?->name ?? 'Produk',
                    'product_sku' => $item->variant?->sku ?? $item->product?->sku ?? '',
                    'variant_name' => $item->variant?->name,
                    'price' => $item->quoted_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]);
            }

            $this->rfq->update(['status' => 'converted']);

            DB::commit();

            $adminEmails = User::role('super_admin')->pluck('email');
            foreach ($adminEmails as $adminEmail) {
                Mail::to($adminEmail)->queue(new RFQAccepted($this->rfq));
            }

            $snapToken = $this->processMidtransPayment($order);

            if ($snapToken) {
                return redirect()->route('checkout.payment', $order->id);
            }

            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Penawaran diterima! Pesanan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    protected function processMidtransPayment(Order $order)
    {
        try {
            Config::$serverKey = config('services.midtrans.server_key');
            Config::$clientKey = config('services.midtrans.client_key');
            Config::$isProduction = config('services.midtrans.is_production');
            Config::$isSanitized = config('services.midtrans.is_sanitized', true);
            Config::$is3ds = config('services.midtrans.is_3ds', true);

            $items = [];
            $calculatedGrossAmount = 0;

            foreach ($order->Items as $item) {
                $unitPrice = (int) round($item->price);
                $items[] = [
                    'id' => 'item-' . $item->id,
                    'price' => $unitPrice,
                    'quantity' => (int) $item->quantity,
                    'name' => substr($item->product_name, 0, 50),
                ];
                $calculatedGrossAmount += ($unitPrice * $item->quantity);
            }

            if ($order->shipping_cost > 0) {
                $shipping = (int) round($order->shipping_cost);
                $items[] = ['id' => 'SHIPPING', 'price' => $shipping, 'quantity' => 1, 'name' => 'Ongkos Kirim'];
                $calculatedGrossAmount += $shipping;
            }

            if ($order->discount_amount > 0) {
                $discount = (int) round($order->discount_amount);
                $items[] = ['id' => 'DISCOUNT', 'price' => -$discount, 'quantity' => 1, 'name' => 'Diskon'];
                $calculatedGrossAmount -= $discount;
            }

            $midtransOrderId = $order->order_number . '-' . time();

            $companyName = $order->customer->company_name ?? $order->customer->name;

            $transactionData = [
                'transaction_details' => [
                    'order_id' => $midtransOrderId,
                    'gross_amount' => $calculatedGrossAmount,
                ],
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => $companyName,
                    'email' => $order->customer->email,
                    'phone' => $order->shipping_phone,
                ],
                'enabled_payments' => [
                    'bca_va', 'mandiri_va', 'bni_va', 'bri_va', 'permata_va', 'cimb_va', 'other_va',
                ],
                'bank_transfer' => [
                    'va_number' => '',
                    'free_text' => [
                        'enquiry' => [
                            [
                                'title' => 'Nama Perusahaan',
                                'description' => $companyName,
                            ],
                        ],
                    ],
                ],
                'credit_card' => ['secure' => true],
            ];

            $snapToken = Snap::getSnapToken($transactionData);

            $order->update([
                'total' => $calculatedGrossAmount,
                'snap_token' => $snapToken,
                'midtrans_order_id' => $midtransOrderId,
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('RFQ Midtrans payment error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function rejectQuotation()
    {
        if (!$this->rfq->isQuoted()) {
            return;
        }

        $this->rfq->update(['status' => 'rejected']);

        $adminEmails = User::role('super_admin')->pluck('email');
        foreach ($adminEmails as $adminEmail) {
            Mail::to($adminEmail)->queue(new RFQRejected($this->rfq));
        }

        session()->flash('success', 'Penawaran ditolak.');
    }

    public function render()
    {
        return view('livewire.customer.rfq-detail', [
            'title' => 'Detail RFQ - ' . $this->rfq->rfq_number,
        ]);
    }
}

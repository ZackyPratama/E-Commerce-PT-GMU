<?php

namespace App\Livewire;

use App\Mail\RFQSubmitted;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.front-end-layout')]
class CartPage extends Component
{
    public $cart;
    public $isB2BApproved = false;

    public function mount()
    {
        $customer = auth()->guard('customer')->user();
        $this->isB2BApproved = $customer && $customer->isB2BApproved();
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
    }

    private function getStockForItem(array $item): int
    {
        if (!empty($item['variant_id'])) {
            $variant = ProductVariant::find($item['variant_id']);
            return $variant ? $variant->stock_quantity : 0;
        }
        $product = Product::find($item['product_id']);
        return $product ? $product->stock_quantity : 0;
    }

    public function getStockForCartKey($cartKey): int
    {
        $cart = session()->get('cart', []);
        if (!isset($cart[$cartKey])) {
            return 0;
        }
        return $this->getStockForItem($cart[$cartKey]);
    }

    public function updateQuantity($cartKey, $quantity)
    {
        if ($quantity < 1) {
            return;
        }
        $cart = session()->get('cart', []);
        if (isset($cart[$cartKey])) {
            $stock = $this->getStockForItem($cart[$cartKey]);
            if ($quantity > $stock) {
                $this->dispatch('error', ['message' => 'Stok tidak mencukupi. Sisa stok: ' . $stock]);
                return;
            }
            $cart[$cartKey]['quantity'] = $quantity;
            session()->put('cart', $cart);
            $this->loadCart();
            $this->dispatch('cart-updated');
        }
    }

    public function removeItem($cartKey)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            $this->loadCart();
            $this->dispatch('cart-updated');
            session()->flash('success', 'Item berhasil dihapus dari keranjang.');
        }
    }

    public function clearCart()
    {
        session()->forget('cart');
        $this->loadCart();
        $this->dispatch('cart-updated');

        session()->flash('success', 'Keranjang berhasil dikosongkan.');
    }

    public function checkout()
    {
        if (!$this->isB2BApproved || empty($this->cart)) {
            return;
        }

        return redirect()->route('checkout');
    }

    public function submitRfq()
    {
        if (!$this->isB2BApproved || empty($this->cart)) {
            return;
        }

        $customer = auth()->guard('customer')->user();

        $rfq = \App\Models\RFQ::create([
            'customer_id' => $customer->id,
            'status' => 'submitted',
            'customer_notes' => 'Permintaan penawaran dari keranjang belanja',
            'subtotal' => $this->subtotal,
            'total' => $this->subtotal,
        ]);

        foreach ($this->cart as $cartKey => $item) {
            $product = Product::find($item['product_id']);
            $variant = null;
            if (!empty($item['variant_id'])) {
                $variant = ProductVariant::find($item['variant_id']);
            }

            \App\Models\RFQItem::create([
                'rfq_id' => $rfq->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'],
                'customer_requested_price' => $item['b2b_price'] ?? $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        session()->forget('cart');
        $this->loadCart();
        $this->dispatch('cart-updated');

        $adminEmails = User::role('super_admin')->pluck('email');
        foreach ($adminEmails as $adminEmail) {
            Mail::to($adminEmail)->queue(new RFQSubmitted($rfq));
        }

        session()->flash('success', 'Permintaan penawaran berhasil dikirim! Admin akan segera mereview harga. Lihat status di menu Permintaan Penawaran.');

        return redirect()->route('customer.rfqs.show', $rfq->id);
    }

    #[Computed]
    public function subtotal()
    {
        return array_sum(array_map(function ($item) {
            return $item['price'] * $item['quantity'];
        }, $this->cart));
    }

    public function render()
    {
        return view('livewire.cart-page', ['title' => 'Keranjang Belanja']);
    }
}

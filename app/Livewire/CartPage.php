<?php

namespace App\Livewire;

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

    public function updateQuantity($cartKey, $quantity)
    {
        if ($quantity < 1) {
            return;
        }
        $cart = session()->get('cart', []);
        if (isset($cart[$cartKey])) {
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

    public function submitRfq()
    {
        if (!$this->isB2BApproved || empty($this->cart)) {
            return;
        }

        session()->put('pending_rfq', [
            'items' => $this->cart,
            'submitted_at' => now()->toDateTimeString(),
        ]);

        session()->flash('success', 'Permintaan penawaran berhasil dikirim! Admin akan segera mereview dan memberikan harga. Anda dapat melihat status di menu Riwayat Penawaran (akan tersedia segera).');
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

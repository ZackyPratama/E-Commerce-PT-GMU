<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
//implementing layout template
// #[Layout('components.layouts.front-end-layout')]
class CartIcon extends Component
{

    public $cartCount = 0;

    public function mount()
    {
        $this->updateCartCount();
    } 

    #[On('cart-updated')]
    public function updateCartCount()
    {
        $cart = session()->get('cart', []);
        $this->cartCount = array_sum(array_column($cart, 'quantity'));
    }
    public function render()
    {
        return view('livewire.cart-icon');
    }
}

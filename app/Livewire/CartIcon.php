<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
//implementing layout template
#[Layout('components.layouts.front-end-layout')]
class CartIcon extends Component
{
    public function render()
    {
        return view('livewire.cart-icon');
    }
}

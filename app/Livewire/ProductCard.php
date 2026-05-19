<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;
    public function addToCart(){
        
    }
    public function render()
    {
        return view('livewire.product-card');
    }
}

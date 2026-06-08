<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

//implementing layout template page (rencana hompage ganti aja bikin baru layout khusus homepage, biar beda sama layout lain yg ada search bar, header, dll)
#[Layout('components.layouts.front-end-layout')]
class Homepage extends Component
{
    public function render()
    {
        $featuredProducts = Product::active()
        ->featured()
        ->inStock()
        ->with(['category', 'brand', 'primaryImage'])
        ->limit(4)
        ->get();

        $categories = Category::active()
        ->sorted()
        ->withCount('products')
        ->limit(6)
        ->get();
        
        $newArrivals = Product::active()
        ->inStock()
        ->with(['category', 'brand', 'primaryImage'])
        ->latest()
        ->limit(4)
        ->get(); 

        return view('livewire.homepage',['featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'newArrivals' => $newArrivals,
        ]);
    }
}

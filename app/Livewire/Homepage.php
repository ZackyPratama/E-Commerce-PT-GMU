<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

//implementing layout template page (rencana hompage ganti aja bikin baru layout khusus homepage, biar beda sama layout lain yg ada search bar, header, dll)
#[Layout('components.layouts.front-end-layout')]
class Homepage extends Component
{
    use WithPagination;

    public function render()
    {
        $flashSaleProducts = Product::active()
        ->inStock()
        ->whereNotNull('compare_price')
        ->whereColumn('compare_price', '>', 'price')
        ->with(['category', 'brand', 'primaryImage'])
        ->latest()
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

        $allProducts = Product::active()
        ->inStock()
        ->with(['category', 'brand', 'primaryImage'])
        ->latest()
        ->paginate(12);

        return view('livewire.homepage',['flashSaleProducts' => $flashSaleProducts,
            'categories' => $categories,
            'newArrivals' => $newArrivals,
            'allProducts' => $allProducts,
        ]);
    }
}

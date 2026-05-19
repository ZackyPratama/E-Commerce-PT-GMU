<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.front-end-layout')]
class ProductDetails extends Component
{
    public Product $product;
    public $selectedVariant = null;
    public $quantity = 1;
    public $selectedImage = null;

    public function mount($slug)
    {
        $this->product = Product::where('slug',$slug)
        ->with(['category','brand','images','variants','approvedReviews.customer'])
        ->firstOrFail();
        
        $this->product->incrementViews();
        
        // set the initial image
        $this->selectedImage = $this->product->primaryImage?->image_path ?? $this->product->images->first()?->image_path;

        // select first variant if product has variants
        if($this->product->has_variants && $this->product->variants->isNotEmpty()){
            $this->selectedVariant = $this->product->variants->first()->id;
        }
        }

        public function selectVariant($variantId){
            $this->selectedVariant = $variantId; 
        } 

        public function selectImage($imagePath){
            $this->selectedImage = $imagePath;
        }

        public function incrementQuantity(){
            $this->quantity++;
        }

        public function decrementQuantity(){
            if($this->quantity > 1){
                $this->quantity--;
            }
        }
        // function untuk menambahkan produk ke keranjang belanja.
        public function addToCart(){
            if($this->product->has_variants && !$this->selectedVariant){
                $this->dispatch('error', ['message' => 'Mohon pilih varian produk terlebih dahulu.  ']);
                //session()->flash('error', 'Mohon pilih varian produk terlebih dahulu.');
                return;
            }

            $cart = session()->get('cart', []);

            $cartKey = $this->selectedVariant ? 'variant' . $this->selectedVariant 
            : 'product' . $this->product->id;

            if(isset($cart[$cartKey])){
                $cart[$cartKey]['quantity'] += $this->quantity;
            }else{
                if($this->selectedVariant){
                    $variant = $this->product->variants->find($this->selectedVariant);
                    $cart[$cartKey] = [
                        'product_id' => $this->product->id,
                        'variant_id' => $variant->id,
                        'name' => $this->product->name, 
                        'variant_name' => $variant->name,
                        'price' => $variant->price,
                        'image' => $this->selectedImage,
                        'quantity' => $this->quantity,
                    ];
                }else{
                    $cart[$cartKey] = [
                        'product_id' => $this->product->id,
                        'variant_id' => null,
                        'name' => $this->product->name, 
                        'variant_name' => null,
                        'price' => $this->product->price,
                        'image' => $this->selectedImage,
                        'quantity' => $this->quantity,
                    ];
                }
            }

            session()->put('cart', $cart);
            $this->dispatch('cart-updated');

            session()->flash('success', 'Produk berhasil ditambahkan ke keranjang!');
        }
    public function render()
    {
        $relatedProducts = Product::active()
        ->where('category_id', $this->product->category_id)
        ->where('id', '!=', $this->product->id)
        ->limit(4)
        ->get();

        return view('livewire.product-details', [
            'relatedProducts' => $relatedProducts,
            'title' => $this->product->name . ' - ' . config('app.name')
            
        ]);
    }
}

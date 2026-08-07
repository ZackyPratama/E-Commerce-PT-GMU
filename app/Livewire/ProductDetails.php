<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.front-end-layout')]
class ProductDetails extends Component
{
    public Product $product;
    public $selectedVariant = null;
    public $quantity = 1;
    public $selectedImage = null;
    public $isB2BApproved = false;

    public function mount($slug)
    {
        $customer = auth()->guard('customer')->user();
        $this->isB2BApproved = $customer && $customer->isB2BApproved();

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

    public function getSelectedVariantModel()
    {
        if ($this->selectedVariant) {
            return $this->product->variants->find($this->selectedVariant);
        }
        return null;
    }

    #[Computed]
    public function maxStock(): int
    {
        if (!$this->product->manage_stock) {
            return 9999;
        }
        if ($this->selectedVariant) {
            $variant = $this->product->variants->find($this->selectedVariant);
            return $variant ? $variant->stock_quantity : 0;
        }
        return $this->product->stock_quantity;
    }

    public function getEffectivePriceProperty()
    {
        $variant = $this->getSelectedVariantModel();
        if ($variant) {
            return $variant->getPriceForCustomer(auth()->guard('customer')->user());
        }
        return $this->product->getPriceForCustomer(auth()->guard('customer')->user());
    }

    public function selectVariant($variantId){
        $this->selectedVariant = $variantId;
        $this->quantity = 1;
    }

    public function selectImage($imagePath){
        $this->selectedImage = $imagePath;
    }

    public function incrementQuantity(){
        if($this->quantity < $this->maxStock){
            $this->quantity++;
        } else {
            $this->dispatch('error', ['message' => 'Stok tidak mencukupi. Sisa stok: ' . $this->maxStock]);
        }
    }

    public function decrementQuantity(){
        if($this->quantity > 1){
            $this->quantity--;
        }
    }

    // function untuk menambahkan produk ke keranjang belanja.
    public function addToCart(){
        if($this->product->has_variants && !$this->selectedVariant){
            $this->dispatch('error', ['message' => 'Mohon pilih varian produk terlebih dahulu.']);
            return;
        }

        if($this->maxStock <= 0){
            $this->dispatch('error', ['message' => 'Stok produk ini habis.']);
            return;
        }

        $cart = session()->get('cart', []);
        $cartKey = $this->selectedVariant ? 'variant' . $this->selectedVariant : 'product' . $this->product->id;

        $existingQty = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $totalQty = $existingQty + $this->quantity;

        if($totalQty > $this->maxStock){
            $remaining = max(0, $this->maxStock - $existingQty);
            $this->dispatch('error', ['message' => 'Stok tidak mencukupi. Sisa stok: ' . $remaining]);
            return;
        }

        if(isset($cart[$cartKey])){
            $cart[$cartKey]['quantity'] += $this->quantity;
        }else{
            if($this->selectedVariant){
                $variant = $this->product->variants->find($this->selectedVariant);
                $price = $this->isB2BApproved && $variant->b2b_price ? (float) $variant->b2b_price : (float) $variant->price;
                $b2bPrice = $this->isB2BApproved ? ($variant->b2b_price ? (float) $variant->b2b_price : null) : null;
                $cart[$cartKey] = [
                    'product_id' => $this->product->id,
                    'variant_id' => $variant->id,
                    'name' => $this->product->name, 
                    'variant_name' => $variant->name,
                    'price' => $price,
                    'b2b_price' => $b2bPrice,
                    'image' => $this->selectedImage,
                    'quantity' => $this->quantity,
                ];
            }else{
                $price = $this->isB2BApproved && $this->product->b2b_price ? (float) $this->product->b2b_price : (float) $this->product->price;
                $b2bPrice = $this->isB2BApproved ? ($this->product->b2b_price ? (float) $this->product->b2b_price : null) : null;
                $cart[$cartKey] = [
                    'product_id' => $this->product->id,
                    'variant_id' => null,
                    'name' => $this->product->name, 
                    'variant_name' => null,
                    'price' => $price,
                    'b2b_price' => $b2bPrice,
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

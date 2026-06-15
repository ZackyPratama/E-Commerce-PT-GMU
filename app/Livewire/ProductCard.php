<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;
    public ?Customer $b2bCustomer = null;
    public bool $isB2BApproved = false;

    public function mount()
    {
        $customer = auth()->guard('customer')->user();
        if ($customer && $customer->isB2BApproved()) {
            $this->b2bCustomer = $customer;
            $this->isB2BApproved = true;
        }
    }

    public function getEffectivePriceProperty(): float
    {
        return $this->product->getPriceForCustomer($this->b2bCustomer);
    }

    public function addToCart()
    {
        if ($this->product->stock_status !== 'in_stock') {
            session()->flash('error', 'Maaf, produk ini sedang tidak tersedia.');
            return;
        }

        $cart = session()->get('cart', []);
        $cartKey = 'product_' . $this->product->id;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $cart[$cartKey] = [
                'product_id' => $this->product->id,
                'variant_id' => null,
                'name' => $this->product->name,
                'variant_name' => null,
                'price' => $this->effectivePrice,
                'b2b_price' => $this->isB2BApproved ? ($this->product->b2b_price ? (float) $this->product->b2b_price : null) : null,
                'image' => $this->product->primaryImage?->image_path,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);
        $this->dispatch('cart-updated');

        session()->flash('success', $this->product->name . ' telah ditambahkan ke keranjang belanja Anda.');
    }
    public function render()
    {
        return view('livewire.product-card');
    }
}

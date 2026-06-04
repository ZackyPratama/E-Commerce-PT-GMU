<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

//implementing layout template
#[Layout('components.layouts.front-end-layout')]
class ProductListing extends Component
{
    // use WithPagination ini untuk mengaktifkan fitur pagination pada komponen Livewire.
    use WithPagination;

    // url untuk category, search, brand, minPrice, maxPrice, sort, feature. Ini memungkinkan pengguna untuk berbagi tautan dengan filter yang sudah diterapkan atau untuk menyimpan preferensi filter mereka dalam URL.
    #[Url]
    public $category = '';
    #[Url]
    public $search = '';
    #[Url]
    public $brand = '';
    #[Url]
    public $minPrice = '';
    #[Url]
    public $maxPrice = '';
    #[Url]
    public $sort = 'newest';
    #[Url]
    public $featured = ' ';
    public $priceRange = [0, 1000000];

    // Mount digunakan untuk menginisialisasi nilai default dari priceRange saat komponen pertama kali dimuat. Ini memastikan bahwa rentang harga memiliki nilai awal yang valid sebelum pengguna melakukan interaksi dengan filter harga.
    public function mount()
    {
        // set harga range berdasarkan produk yang available
        $maxProductPrice = Product::max('price') ?? 1000000;
        $this->priceRange = [0, ceil($maxProductPrice)]; //ceil digunakan untuk membulatkan ke atas agar mencakup harga maksimum produk yang tersedia.

        // Set nilai default untuk maxPrice jika tidak ada nilai yang diberikan melalui URL. Ini memastikan bahwa filter harga memiliki batas atas yang valid bahkan jika pengguna tidak menentukan nilai maxPrice dalam URL.
        if (empty($this->maxPrice)) {
            $this->maxPrice = $this->priceRange[1];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage(); // reset pagination ke halaman pertama setiap kali search diupdate
    }
    public function updatingCategory()
    {
        $this->resetPage(); // reset pagination ke halaman pertama setiap kali category diupdate
    }
    public function updatingBrand()
    {
        $this->resetPage(); // reset pagination ke halaman pertama setiap kali brand diupdate
    }
    public function updatingSort()
    {
        $this->resetPage(); // reset pagination ke halaman pertama setiap kali sort diupdate
    }
    public function applyPriceFilter()
    {
        $this->resetPage(); // reset pagination ke halaman pertama setiap kali price filter diupdate
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'brand', 'minPrice', 'maxPrice', 'featured']);
        $this->maxPrice = $this->priceRange[1]; // reset maxPrice ke nilai maksimum dari priceRange
        $this->resetPage(); // reset pagination ke halaman pertama setelah menghapus filter
    }

    public function render()
    {
        // Query builder untuk mengambil produk yang aktif dan memuat relasi category, brand, dan primaryImage. Ini memastikan bahwa data produk yang ditampilkan sudah lengkap dengan informasi kategori, merek, dan gambar utama yang terkait.
        $query = Product::query()
            ->active()
            ->with(['category', 'brand', 'primaryImage']);

        // search 
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');

            });
        }

        //category filter
        if ($this->category) {
            $categoryModel = Category::where('slug', $this->category)->first();
            if ($categoryModel) {
                $query->where('category_id', $categoryModel->id);
            }
        }
        //brand filter
        if ($this->brand) {
            $brandModel = Brand::where('slug', $this->brand)->first();
            if ($brandModel) {
                $query->where('brand_id', $brandModel->id);
            }
        }

        //price range filter
        if ($this->minPrice !== '' || $this->maxPrice !== '') {
            $min = $this->minPrice ?: 0;
            $max = $this->maxPrice ?: $this->priceRange[1];
            $query->whereBetween('price', [$min, $max]);
        }

        //featured filter
        if ($this->featured) {
            $query->featured();
        }

        //sorting
        match ($this->sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'popular' => $query->orderBy('views_count', 'desc'),
            default => $query->latest()
        };

        // pagination  12 produk per halaman
        $products = $query->paginate(12);

        $categories = Category::active()
            ->sorted()
            ->withCount('products')
            ->get();

        $brands = Brand::active()
            ->sorted()
            ->withCount('products')
            ->get();


        return view('livewire.product-listing', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes, HasFactory;
    // fillable attributes for mass assignment
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'stock_quantity',
        'low_stock_threshold',
        'manage_stock',
        'stock_status',
        'is_active',
        'is_featured',
        'has_variants',
        'weight',
        'meta_title',
        'meta_description',
        'view_count',
    ];

    protected function casts(): array
    {

        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'weight' => 'decimal:2',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'views_count' => 'integer',
            'manage_stock' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'has_variants' => 'boolean',

        ];
    }

    // scope to only active products
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    // scope to only featured products
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    // scope to only in stock products
    #[Scope]
    protected function inStock(Builder $query): void
    {
        $query->where('stock_status', 'in_stock')
            ->where('stock_quantity', '>', 0);
    }

    // scope to products with low stock
    #[Scope]
    protected function lowStock(Builder $query): void
    {
        $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quality', '>', 0);
    }

    // scope to filter by category
    #[Scope]
    protected function inCategory(Builder $query, int $categoryId): void
    {
        $query->where('category_id', $categoryId);
    }

    // scope to filter by brand
    #[Scope]
    protected function ofBrand(Builder $query, int $brandId): void
    {
        $query->where('brand_id', $brandId);
    }


    // scope to filter by price range
    #[Scope]
    protected function priceBetween(Builder $query, float $min, float $max): void
    {
        $query->whereBetween('price', [$min, $max]);
    }

    // Relationship to Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship to Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Relationship to ProductVariant
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Relationship to ProductImage
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    // relationship to primary image    public function primaryImage()
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    // Relationship to Review
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    //relationship to approvalsReviews
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    // Helper method
    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return 0;
    }

    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    // events
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

}

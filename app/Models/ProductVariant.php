<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    //fillable attributes for mass assignment
    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'options',
        'price',
        'compare_price',
        'stock_quantity',
        'stock_status',
        'is_active',
        'sort_order',
    ];

    protected function casts() : array
    {
        return [
            'options' => 'array',
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // scope to only active variants
    #[Scope]
    protected function active(Builder $query) : void
    {
        $query->where('is_active', true);
    }

    // scope to only in stock variants
    #[Scope]
    protected function inStock(Builder $query) : void
    {
        $query->where('stock_status', 'in_stock')
            ->where('stock_quantity', '>', 0);
    }

    // Relationship to Product
    public function product()   
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship to ProductImage
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Relationship to OrderItem
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);  
    }

    // Helper method to get discount percentage
    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return 0;
    }

    // event hooks to auto-generate SKU and slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = 'VAR-' . strtoupper(Str::random(8));
            }
        });
    }
}

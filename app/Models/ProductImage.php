<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    //fillable attributes for mass assignment
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'image_path',
        'alt_text',
        'is_primary',
        'sort_order',
    ];
    protected function casts() : array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //scope to only primary images
    #[Scope]
    protected function primary(Builder $query) : void
    {
        $query->where('is_primary', true);
    }

    // Relationship to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship to ProductVariant
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Helper method to get full URL of the image
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}

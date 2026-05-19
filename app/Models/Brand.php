<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;
    // Mass assignable attributes
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'is_active',
        'sort_order',
    ];

    // scope to filter active brands
    #[Scope()]
    protected function active(Builder $builder)
    {
        return $builder->where('is_active', true);
    }

    // scope to sort brands by sort_order
    #[Scope()]
    protected function sorted(Builder $builder)
    {
        return $builder->orderBy('sort_order', 'asc');
    }

    // Relationships with products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // function to automatically generate slug from name if not provided
    protected static function boot()
    {
        parent::boot();
        
        // Generate slug from name if not provided on creating and updating
        static::creating(function($brand){
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });

        static::updating(function($brand){
            if ($brand->isDirty('name') && empty($brand->empty)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }
}

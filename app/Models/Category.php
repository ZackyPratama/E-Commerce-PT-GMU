<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    // fillable attributes for mass assignment
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    // scope to filter active categories
    #[Scope]
    protected function active(Builder $builder)
    {
        return $builder->where('is_active', true);
    }

    // scope to sort categories by sort_order
    #[Scope]
    protected function sorted(Builder $builder)
    {
        return $builder->orderBy('sort_order', 'asc');
    }

    // Relationships with products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    
    // function static boot to automatically generate slug from name if not provided on creating and updating
    protected static function boot()
    {
        parent::boot();
        
        // Generate slug from name if not provided on creating and updating
        static::creating(function($category){
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function($category){
            if ($category->isDirty('name') && empty($category->empty)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    //fillable attributes for mass assignment
    protected $fillable = [
        'product_id',
        'customer_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'is_verified_purchase',
        'is_approved',
    ];

    protected function casts() : array
    {
        return [
            'rating' => 'integer',
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    // scope to only approved reviews
    #[Scope]
    protected function approved(Builder $query) : void
    {
        $query->where('is_approved', true);
    }

    // scope to only reviews for verified purchases
    #[Scope]
    protected function verified(Builder $query) : void
    {
        $query->where('is_verified_purchase', true);
    }

    // scope to only reviews with a certain rating
    #[Scope]
    protected function rating(Builder $query, int $rating) : void
    {
        $query->where('rating', $rating);   
    }

    // Relationship to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relationship to Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship to Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}

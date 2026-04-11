<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    // fillable attributes for mass assignment
    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'notes',
    ];

    // Relationship to Order
    public function order()
    {      
        return $this->belongsTo(Order::class);
    }

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

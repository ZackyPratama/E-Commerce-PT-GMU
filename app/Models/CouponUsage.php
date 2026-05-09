<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    use HasFactory;
    protected $fillable = [
        'coupon_id',
        'customer_id',
        'order_id',
    ];

    // relationship to the Coupon model
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    // relationship to the Customer model
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // relationship to the Order model
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

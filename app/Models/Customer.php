<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Address;
use App\Models\Review;
use App\Models\CouponUsage;

class Customer extends Authenticable
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'date_of_birth',
        'gender',
        'is_active',
        'remember_token',
        'email_verified_at',
        'type',
        'company_name',
        'company_registration_number',
        'b2b_status',
        'approved_at',
        'rejection_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',

    ];

    protected function casts(): array
    {
        return [
            'email-verified-at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    #[Scope()]
    protected function active(Builder $builder)
    {
        $builder->where('is_active', true);
    }

    // B2B methods
    public function isB2B(): bool
    {
        return $this->type === 'b2b';
    }

    public function isB2BApproved(): bool
    {
        return $this->type === 'b2b' && $this->b2b_status === 'approved';
    }

    public function isB2BPending(): bool
    {
        return $this->type === 'b2b' && $this->b2b_status === 'pending';
    }

    public function isB2BRejected(): bool
    {
        return $this->type === 'b2b' && $this->b2b_status === 'rejected';
    }

    #[Scope()]
    protected function b2bPending(Builder $builder)
    {
        $builder->where('type', 'b2b')->where('b2b_status', 'pending');
    }

     // Relationships
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddresss()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    //helper method
    public function getTotalSpentAttribute()
    {
        return $this->orders()->where('payment_status', 'paid')->sum('total_amount');
    }

    public function getOrderCountAttribute()
    {
        return $this->orders()->count();
    }
}

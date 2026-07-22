<?php

namespace App\Models;

use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    use HasFactory;
    //fillable attributes for mass assignment
    protected $fillable = [
        'order_number',
        'customer_id',
        'coupon_id',
        'subtotal',
        'discount_amount',
        'shipping_cost',
        'tax_amount',
        'total',
        'shipping_full_name',
        'shipping_phone',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'payment_method',
        'payment_status',
        'transaction_id',
        'status',
        'tracking_number',
        'customer_notes',
        'admin_notes',
        'snap_token',
        'midtrans_order_id',
        'payment_completed_at',
        'rfq_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'payment_status' => PaymentStatusEnum::class,
        ];
    }

    // Scope to filter orders by status
    #[Scope]
    protected function ofStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }

    // Scope to filter orders by payment status
    #[Scope]
    protected function paymentStatus(Builder $query, string $status): void
    {
        $query->where('payment_status', $status);
    }

    //Scope to only pending orders
    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', 'pending');
    }

    // Scope to only processing orders
    #[Scope]
    protected function processing(Builder $query): void
    {
        $query->where('status', 'processing');
    }

    // Scope to only shipped orders
    #[Scope]
    protected function shipped(Builder $query): void
    {
        $query->where('status', 'shipped');
    }

    // Scope to only delivered orders
    #[Scope]
    protected function delivered(Builder $query): void
    {
        $query->where('status', 'delivered');
    }

    #[Scope]
    protected function paid(Builder $query): void
    {
        $query->whereIn('payment_status', [PaymentStatusEnum::PAID, PaymentStatusEnum::COMPLETED]);
    }

    // Helper Method

    public function getShippingAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->shipping_address_line_1,
            $this->shipping_address_line_2,
            $this->shipping_city,
            $this->shipping_state,
            $this->shipping_postal_code,
            $this->shipping_country
        ]));
    }

    public function updateStatus($newStatus, $notes = null, $userid = null)
    {
        $this->update(['status' => $newStatus]);

        $this->statusHistories()->create([
            'status' => $newStatus,
            'notes' => $notes,
            'user_id' => $userid
        ]);
    }

    // relationships to customer 
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // relationship to coupon
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    // relationship to RFQ
    public function rfq()
    {
        return $this->belongsTo(RFQ::class);
    }

    // relationship to order items
    public function Items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // relationship to order status history
    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }


    // Boot method to generate order number and create initial status history
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if ((empty($order->order_number))) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });

        static::created(function ($order) {
            $order->statusHistories()->create([
                'status' => $order->status,
                'notes' => 'Order created',

            ]);

            // order confirmation email logic can be added here soon
        });
    }


}

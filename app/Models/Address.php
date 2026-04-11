<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    // Mass assignable attributes
    protected $fillable = [
        'customer_id',
        'full_name',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
        'type',
    ];

    // function to cast attributes to appropriate data types
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    // Local scopes for querying addresses
    #[Scope()]
    protected function default(Builder $builder)
    {
        $builder->where('is_default', true);
    }

    // Scope to filter addresses by type (e.g., 'billing', 'shipping')
    #[Scope()]
    protected function ofType(Builder $builder, string $type)
    {
        $builder->where('type', $type);
    }

    //relationships with customer
    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    //function to get full address as a single string
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]));
    }
}

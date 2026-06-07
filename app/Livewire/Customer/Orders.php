<?php

namespace App\Livewire\Customer;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
//implementing layout template
#[Layout('components.layouts.front-end-layout')]
class Orders extends Component
{
    use WithPagination;

    public $statusFilter = '';

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
    public function render()
    {
        $query = auth('customer')->user()->orders()
        ->with(['orderItems.product'])
        ->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->paginate(10);
        return view('livewire.orders', [
            'orders' => $orders,
        ]);
    }
}

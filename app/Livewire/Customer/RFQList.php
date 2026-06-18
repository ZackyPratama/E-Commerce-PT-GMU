<?php

namespace App\Livewire\Customer;

use App\Models\RFQ;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.front-end-layout')]
class RFQList extends Component
{
    use WithPagination;

    public function render()
    {
        $customer = auth('customer')->user();

        $rfqs = RFQ::forCustomer($customer)
            ->with(['items'])
            ->latest()
            ->paginate(10);

        return view('livewire.customer.rfq-list', [
            'rfqs' => $rfqs,
            'title' => 'Permintaan Penawaran',
        ]);
    }
}

<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;

//implementing layout template
#[Layout('components.layouts.front-end-layout')]
class Dashboard extends Component
{

    public function render()
    {
        // variable customer utk menyimpan data customer yg sedang login, menggunakan guard 'customer' karena kita punya guard khusus utk customer di auth.php (dari copilot suggestion)
        /** @var Customer $customer */

        $customer = auth('customer')->user();

        $recentOrders = $customer->orders()
            ->with(['items.product'])
            ->latest()
            ->limit(5)
            ->get();

        $isB2B = $customer->isB2BApproved();

        $stats = [
            'total_orders' => $customer->orders()->count(),
            'pending_orders' => $customer->orders()->where('status', 'pending')->count(),
            'total_spent' => $customer->orders()->where('payment_status', 'paid')->sum('total'),
            'rfq_count' => $isB2B ? $customer->rfqs()->count() : 0,
            'rfq_pending' => $isB2B ? $customer->rfqs()->whereIn('status', ['submitted', 'under_review'])->count() : 0,
        ];

        $recentRfqs = $isB2B ? $customer->rfqs()->latest()->limit(5)->get() : collect();

        return view('livewire.customer.dashboard', [
            'recentOrders' => $recentOrders,
            'recentRfqs' => $recentRfqs,
            'stats' => $stats,
            'isB2B' => $isB2B,
        ]);
    }
}

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


        $stats = [
            'total_orders' => $customer->orders()->count(),
            'pending_orders' => $customer->orders()->where('status', 'pending')->count(),
            'total_spent' => $customer->orders()->where('payment_status', 'paid')->sum('total'),

        ];
        return view('livewire.customer.dashboard', [
            'recentOrders' => $recentOrders,
            'stats' => $stats,
        ]);
    }
}

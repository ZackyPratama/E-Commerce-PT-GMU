<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.front-end-layout')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.customer.dashboard');
    }
}

<?php

namespace App\Livewire\Customer;

use Livewire\Attributes\Layout;
use Livewire\Component;

//implementing layout template
#[Layout('components.layouts.front-end-layout')]
class Profile extends Component
{
    public function render()
    {
        return view('livewire.customer.profile');
    }
}

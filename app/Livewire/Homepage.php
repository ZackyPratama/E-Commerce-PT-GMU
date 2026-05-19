<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

//implementing layout template page (rencana hompage ganti aja bikin baru layout khusus homepage, biar beda sama layout lain yg ada search bar, header, dll)
#[Layout('components.layouts.front-end-layout')]
class Homepage extends Component
{
    public function render()
    {
        return view('livewire.homepage');
    }
}

<?php

namespace App\Filament\Auth\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        if (auth()->user()?->hasRole('owner')) {
            return redirect()->to('/admin/owner');
        }

        return redirect()->intended('/admin');
    }
}

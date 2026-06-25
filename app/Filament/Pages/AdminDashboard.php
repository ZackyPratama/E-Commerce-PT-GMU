<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class AdminDashboard extends Dashboard
{
    public static function canAccess(): bool
    {
        return auth()->user() && !auth()->user()->hasRole('owner');
    }
}

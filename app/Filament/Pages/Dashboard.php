<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\StatsOverview;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $title = 'Dashboard';
    protected static ?string $slug = 'Dashboard';

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('Admin');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class RoleWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $roles = $user?->getRoleNames()->toArray() ?? [];

        return [
            Stat::make('Username', $user?->name ?? '-')
                ->description($user?->email ?? '-')
                ->color('primary')
                ->extraAttributes(['class' => 'col-span-6']),

            Stat::make('Role Pengguna', implode(', ', $roles) ?: 'Tidak ada role')
                ->description('Role aktif saat ini')
                ->color('success')
                ->extraAttributes(['class' => 'col-span-6']),
        ];
    }
}

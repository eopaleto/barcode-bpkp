<?php

namespace App\Filament\Traits;

trait SuperAdminReadOnly
{
    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return true;
    }
    public static function canCreate(): bool
    {
        return ! auth()->user()?->hasRole('SuperAdmin');
    }

    public static function canEdit($record): bool
    {
        return ! auth()->user()?->hasRole('SuperAdmin');
    }

    public static function canDelete($record): bool
    {
        return ! auth()->user()?->hasRole('SuperAdmin');
    }

    public static function canForceDelete($record): bool
    {
        return ! auth()->user()?->hasRole('SuperAdmin');
    }

    public static function canRestore($record): bool
    {
        return ! auth()->user()?->hasRole('SuperAdmin');
    }
}

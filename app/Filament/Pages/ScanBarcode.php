<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ScanBarcode extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $slug = 'ScanBarcode';
    protected static ?string $navigationLabel = 'Scan Barcode';
    protected static ?string $navigationGroup= 'Operator';
    protected static string $view = 'filament.pages.scan-barcode';

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user?->hasRole('Operator');
    }
}

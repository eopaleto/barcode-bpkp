<?php

namespace App\Filament\Resources\PergiResource\Pages;

use App\Filament\Resources\PergiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPergis extends ListRecords
{
    protected static string $resource = PergiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data Baru')
                ->icon('heroicon-o-plus')
        ];
    }
}

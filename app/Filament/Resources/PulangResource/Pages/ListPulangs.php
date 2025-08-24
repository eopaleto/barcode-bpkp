<?php

namespace App\Filament\Resources\PulangResource\Pages;

use App\Filament\Resources\PulangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPulangs extends ListRecords
{
    protected static string $resource = PulangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data Baru')
                ->icon('heroicon-o-plus')
        ];
    }
}

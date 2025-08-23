<?php

namespace App\Filament\Resources\PulangResource\Pages;

use App\Filament\Resources\PulangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPulang extends EditRecord
{
    protected static string $resource = PulangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

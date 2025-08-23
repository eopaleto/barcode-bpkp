<?php

namespace App\Filament\Resources\PergiResource\Pages;

use App\Filament\Resources\PergiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPergi extends EditRecord
{
    protected static string $resource = PergiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

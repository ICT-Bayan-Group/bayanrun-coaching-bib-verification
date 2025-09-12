<?php

namespace App\Filament\Resources\PemainBolaResource\Pages;

use App\Filament\Resources\PemainBolaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPemainBola extends EditRecord
{
    protected static string $resource = PemainBolaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\PemainBolaResource\Pages;

use App\Filament\Resources\PemainBolaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPemainBola extends ViewRecord
{
    protected static string $resource = PemainBolaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }
}
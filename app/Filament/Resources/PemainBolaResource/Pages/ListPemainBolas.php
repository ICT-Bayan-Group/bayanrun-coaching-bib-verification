<?php

namespace App\Filament\Resources\PemainBolaResource\Pages;

use App\Filament\Resources\PemainBolaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPemainBolas extends ListRecords
{
    protected static string $resource = PemainBolaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

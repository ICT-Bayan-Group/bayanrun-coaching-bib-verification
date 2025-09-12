<?php

namespace App\Filament\Resources\SekolahBolaResource\Pages;

use App\Filament\Resources\SekolahBolaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSekolahBolas extends ListRecords
{
    protected static string $resource = SekolahBolaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

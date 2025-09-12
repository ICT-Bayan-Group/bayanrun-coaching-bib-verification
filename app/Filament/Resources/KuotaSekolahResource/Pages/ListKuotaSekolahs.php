<?php

namespace App\Filament\Resources\KuotaSekolahResource\Pages;

use App\Filament\Resources\KuotaSekolahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKuotaSekolahs extends ListRecords
{
    protected static string $resource = KuotaSekolahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
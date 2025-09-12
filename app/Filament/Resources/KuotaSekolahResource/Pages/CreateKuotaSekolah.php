<?php

namespace App\Filament\Resources\KuotaSekolahResource\Pages;

use App\Filament\Resources\KuotaSekolahResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKuotaSekolah extends CreateRecord
{
    protected static string $resource = KuotaSekolahResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['updated_by'] = auth()->id();
        return $data;
    }
}
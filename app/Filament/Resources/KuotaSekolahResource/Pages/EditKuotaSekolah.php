<?php

namespace App\Filament\Resources\KuotaSekolahResource\Pages;

use App\Filament\Resources\KuotaSekolahResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKuotaSekolah extends EditRecord
{
    protected static string $resource = KuotaSekolahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        return $data;
    }
}
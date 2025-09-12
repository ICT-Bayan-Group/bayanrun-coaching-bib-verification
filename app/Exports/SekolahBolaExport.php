<?php

namespace App\Exports;

use App\Models\SekolahBola;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SekolahBolaExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return SekolahBola::select('nama', 'pic', 'email', 'telepon', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['Nama Sekolah Bola', 'PIC', 'Email', 'Nomor Telepon', 'Dibuat'];
    }
}

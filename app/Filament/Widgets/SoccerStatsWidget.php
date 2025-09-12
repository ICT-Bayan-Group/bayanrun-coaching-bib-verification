<?php

namespace App\Filament\Widgets;

use App\Models\SekolahBola;
use App\Models\PemainBola;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SoccerStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $totalSekolah = SekolahBola::count();
        $totalPemain = PemainBola::count();
        
        // Pendaftaran hari ini
        $sekolahHariIni = SekolahBola::whereDate('created_at', today())->count();
        $pemainHariIni = PemainBola::whereDate('created_at', today())->count();
        
        // Pendaftaran minggu ini vs minggu lalu
        $sekolahMingguIni = SekolahBola::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $sekolahMingguLalu = SekolahBola::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        
        $pemainMingguIni = PemainBola::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $pemainMingguLalu = PemainBola::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();

        // Hitung persentase perubahan
        $sekolahTrend = $sekolahMingguLalu > 0 ? (($sekolahMingguIni - $sekolahMingguLalu) / $sekolahMingguLalu * 100) : 0;
        $pemainTrend = $pemainMingguLalu > 0 ? (($pemainMingguIni - $pemainMingguLalu) / $pemainMingguLalu * 100) : 0;

        // Rata-rata pemain per sekolah
        $rataRataPemain = $totalSekolah > 0 ? round($totalPemain / $totalSekolah, 1) : 0;

        // Pemain per kategori
        $kategoriStats = PemainBola::select('umur_kategori', DB::raw('count(*) as total'))
            ->groupBy('umur_kategori')
            ->pluck('total', 'umur_kategori')
            ->toArray();

        return [
            Stat::make('Total SSB', $totalSekolah)
                ->description($sekolahHariIni . ' pendaftaran hari ini')
                ->descriptionIcon('heroicon-m-building-office')
                ->chart($this->getSekolahChartData())
                ->color('success'),

            Stat::make('Total Pemain SSB', $totalPemain)
                ->description($pemainHariIni . ' pemain baru hari ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart($this->getPemainChartData())
                ->color('info'),

            Stat::make('Pendaftaran SSB Minggu Ini', $sekolahMingguIni)
                ->description(($sekolahTrend >= 0 ? '+' : '') . number_format($sekolahTrend, 1) . '% dari minggu lalu')
                ->descriptionIcon($sekolahTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($sekolahTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Kategori 7-8 Tahun', $kategoriStats['7-8'] ?? 0)
                ->description('Pemain')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),

            Stat::make('Kategori 9-10 Tahun', $kategoriStats['9-10'] ?? 0)
                ->description('Pemain')
                ->descriptionIcon('heroicon-m-user')
                ->color('warning'),

            Stat::make('Kategori 11-12 Tahun', $kategoriStats['11-12'] ?? 0)
                ->description('Pemain')
                ->descriptionIcon('heroicon-m-user')
                ->color('success'),
        ];
    }

    private function getSekolahChartData(): array
    {
        return SekolahBola::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }

    private function getPemainChartData(): array
    {
        return PemainBola::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }
}
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SekolahBolaResource\Pages;
use App\Filament\Resources\SekolahBolaResource\RelationManagers\PemainBolasRelationManager;
use App\Models\SekolahBola;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class SekolahBolaResource extends Resource
{
    protected static ?string $model = SekolahBola::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Daftar SSB';
    protected static ?string $pluralLabel = 'Daftar Sekolah Sepak Bola';
    protected static ?string $modelLabel = 'Daftar SSB';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Sekolah Bola')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('pic')
                    ->label('PIC')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('telepon')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->required()
                    ->maxLength(20),

                Forms\Components\TextInput::make('user_token')
                    ->label('User Token')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record) => $record !== null)
                    ->helperText('Token ini dibuat otomatis dan tidak dapat diubah'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Sekolah Bola')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pic')
                    ->label('PIC')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('telepon')
                    ->label('Nomor Telepon'),
                    
                Tables\Columns\TextColumn::make('pemain_bolas_count')
                    ->label('Jumlah Pemain')
                    ->counts('pemainBolas')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('user_link')
                    ->label('User Link')
                    ->getStateUsing(fn ($record) => url("/user/{$record->user_token}"))
                    ->url(fn ($record) => url("/user/{$record->user_token}"))
                    ->openUrlInNewTab()
                    ->copyable()
                    ->copyMessage('Link berhasil disalin!')
                    ->copyMessageDuration(1500)
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-link')
                    ->formatStateUsing(fn ($state) => 'Buka Link'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                // Action untuk melihat pemain dengan modal sederhana
                Tables\Actions\Action::make('lihat_pemain')
                    ->label('Lihat Pemain')
                    ->icon('heroicon-m-users')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'Daftar Pemain - ' . $record->nama)
                    ->modalContent(function ($record) {
                        $pemainBolas = $record->pemainBolas()->orderBy('nama')->get();
                        
                        if ($pemainBolas->isEmpty()) {
                            return view('filament::components.empty-state', [
                                'icon' => 'heroicon-o-user-group',
                                'heading' => 'Belum Ada Pemain',
                                'description' => 'Sekolah bola ini belum memiliki pemain terdaftar.',
                            ]);
                        }
                        
                        // Menggunakan HTML string langsung untuk menghindari masalah view
                        $html = '<div class="space-y-4">';
                        $html .= '<div class="mb-4 text-sm text-gray-600 dark:text-gray-400">Total: ' . $pemainBolas->count() . ' pemain</div>';
                        $html .= '<div class="overflow-x-auto">';
                        $html .= '<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">';
                        $html .= '<thead class="bg-gray-50 dark:bg-gray-800">';
                        $html .= '<tr>';
                        $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>';
                        $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Umur</th>';
                        $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>';
                        $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terdaftar</th>';
                        $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '<tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">';
                        
                        foreach ($pemainBolas as $pemain) {
                            $badgeColor = match($pemain->umur_kategori) {
                                '7-8' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                                '9-10' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                '11-12' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
                            };
                            
                            $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800">';
                            $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">' . e($pemain->nama) . '</td>';
                            $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">' . $pemain->umur . ' tahun</td>';
                            $html .= '<td class="px-6 py-4 whitespace-nowrap">';
                            $html .= '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $badgeColor . '">';
                            $html .= $pemain->umur_kategori . ' tahun';
                            $html .= '</span>';
                            $html .= '</td>';
                            $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">' . $pemain->created_at->format('d M Y') . '</td>';
                            $html .= '</tr>';
                        }
                        
                        $html .= '</tbody>';
                        $html .= '</table>';
                        $html .= '</div>';
                        $html .= '</div>';
                        
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalWidth('5xl')
                    ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename(fn () => 'data-sekolah-bola-' . date('Y-m-d'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                                ->withColumns([
                                    Column::make('nama')->heading('Nama Sekolah Bola'),
                                    Column::make('pic')->heading('PIC'),
                                    Column::make('email')->heading('Email'),
                                    Column::make('telepon')->heading('Nomor Telepon'),
                                    Column::make('user_token')->heading('User Token'),
                                    Column::make('pemain_bola_count')->heading('Jumlah Pemain'),
                                    Column::make('user_link')
                                        ->heading('User Link')
                                        ->formatStateUsing(fn ($state, $record) => url("/user/{$record->user_token}")),
                                    Column::make('created_at')
                                        ->heading('Tanggal Dibuat')
                                        ->formatStateUsing(fn ($state) => $state?->format('d/m/Y H:i')),
                                ])
                        ])
                        ->label('Export Excel'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename(fn () => 'semua-data-sekolah-bola-' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->withColumns([
                                Column::make('nama')->heading('Nama Sekolah Bola'),
                                Column::make('pic')->heading('PIC'),
                                Column::make('email')->heading('Email'),
                                Column::make('telepon')->heading('Nomor Telepon'),
                                Column::make('user_token')->heading('User Token'),
                                Column::make('pemain_bola_count')->heading('Jumlah Pemain'),
                                Column::make('user_link')
                                    ->heading('User Link')
                                    ->formatStateUsing(fn ($state, $record) => url("/user/{$record->user_token}")),
                                Column::make('created_at')
                                    ->heading('Tanggal Dibuat')
                                    ->formatStateUsing(fn ($state) => $state?->format('d/m/Y H:i')),
                            ])
                    ])
                    ->label('Export Semua Data')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PemainBolasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSekolahBolas::route('/'),
            'create' => Pages\CreateSekolahBola::route('/create'),
            'edit' => Pages\EditSekolahBola::route('/{record}/edit'),
            'view' => Pages\ViewSekolahBola::route('/{record}'),
        ];
    }
}
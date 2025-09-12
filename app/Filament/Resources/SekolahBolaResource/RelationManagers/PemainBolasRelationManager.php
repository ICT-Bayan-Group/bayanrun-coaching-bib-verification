<?php

namespace App\Filament\Resources\SekolahBolaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PemainBolasRelationManager extends RelationManager
{
    protected static string $relationship = 'pemainBolas';

    protected static ?string $title = 'Daftar Pemain';
    protected static ?string $modelLabel = 'Pemain';
    protected static ?string $pluralModelLabel = 'Pemain';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Pemain')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('umur')
                    ->label('Umur (Tahun)')
                    ->numeric()
                    ->required()
                    ->minValue(7)
                    ->maxValue(12)
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        // Auto-set kategori umur berdasarkan umur
                        if ($state >= 7 && $state <= 8) {
                            $set('umur_kategori', '7-8');
                        } elseif ($state >= 9 && $state <= 10) {
                            $set('umur_kategori', '9-10');
                        } elseif ($state >= 11 && $state <= 12) {
                            $set('umur_kategori', '11-12');
                        }
                    }),

                Forms\Components\Select::make('umur_kategori')
                    ->label('Kategori Umur')
                    ->options([
                        '7-8' => '7-8 Tahun',
                        '9-10' => '9-10 Tahun',
                        '11-12' => '11-12 Tahun',
                    ])
                    ->required()
                    ->helperText('Kategori akan otomatis terisi berdasarkan umur'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Pemain')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('umur')
                    ->label('Umur')
                    ->suffix(' tahun')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('umur_kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '7-8' => 'info',
                        '9-10' => 'warning',
                        '11-12' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y H:i')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('umur_kategori')
                    ->label('Kategori Umur')
                    ->options([
                        '7-8' => '7-8 Tahun',
                        '9-10' => '9-10 Tahun',
                        '11-12' => '11-12 Tahun',
                    ]),

                Tables\Filters\SelectFilter::make('umur')
                    ->label('Umur')
                    ->options([
                        7 => '7 Tahun',
                        8 => '8 Tahun',
                        9 => '9 Tahun',
                        10 => '10 Tahun',
                        11 => '11 Tahun',
                        12 => '12 Tahun',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Pemain')
                    ->modalHeading('Tambah Pemain Baru')
                    ->successNotificationTitle('Pemain berhasil ditambahkan'),
                    
                // Export Action
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename(fn () => 'pemain-' . str_replace(' ', '-', strtolower($this->getOwnerRecord()->nama)) . '-' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->withColumns([
                                Column::make('nama')->heading('Nama Pemain'),
                                Column::make('umur')->heading('Umur'),
                                Column::make('umur_kategori')->heading('Kategori Umur'),
                                Column::make('created_at')
                                    ->heading('Tanggal Daftar')
                                    ->formatStateUsing(fn ($state) => $state?->format('d/m/Y')),
                            ])
                    ])
                    ->label('Export Pemain')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading(fn ($record) => 'Detail Pemain: ' . $record->nama),
                Tables\Actions\EditAction::make()
                    ->modalHeading(fn ($record) => 'Edit Pemain: ' . $record->nama),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Pemain')
                    ->modalDescription('Apakah Anda yakin ingin menghapus pemain ini?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Export bulk action
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename(fn () => 'selected-pemain-' . str_replace(' ', '-', strtolower($this->getOwnerRecord()->nama)) . '-' . date('Y-m-d'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                        ])
                        ->label('Export Terpilih')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success'),
                        
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pemain Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus pemain yang dipilih?'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Pemain')
            ->emptyStateDescription('Sekolah bola ini belum memiliki pemain terdaftar.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->striped();
    }
}
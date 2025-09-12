<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemainBolaResource\Pages;
use App\Models\PemainBola;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PemainBolaResource extends Resource
{
    protected static ?string $model = PemainBola::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Pemain SSB';
    protected static ?string $pluralLabel = 'Daftar Pemain SSB';
    protected static ?string $modelLabel = 'Pemain SSB';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('sekolah_bola_id')
                ->relationship('sekolahBola', 'nama')
                ->label('Sekolah Bola')
                ->required(),

            Forms\Components\TextInput::make('nama')
                ->label('Nama Pemain')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('umur')
                ->label('Umur')
                ->numeric()
                ->required()
                ->minValue(7)
                ->maxValue(12),

            Forms\Components\Select::make('umur_kategori')
                ->label('Kategori Umur')
                ->options([
                    '7-8' => '7-8 Tahun',
                    '9-10' => '9-10 Tahun',
                    '11-12' => '11-12 Tahun',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Pemain')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('umur')
                    ->label('Umur')
                    ->sortable()
                    ->suffix(' tahun'),

                Tables\Columns\TextColumn::make('umur_kategori')
                    ->label('Kategori')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sekolahBola.nama')
                    ->label('Sekolah Bola')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('umur_kategori')
                    ->label('Kategori Umur')
                    ->options([
                        '7-8' => '7-8 Tahun',
                        '9-10' => '9-10 Tahun',
                        '11-12' => '11-12 Tahun',
                    ]),

                Tables\Filters\SelectFilter::make('sekolah_bola_id')
                    ->label('Sekolah Bola')
                    ->relationship('sekolahBola', 'nama'),

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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename(fn () => 'data-pemain-bola-' . date('Y-m-d'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                                ->withColumns([
                                    Column::make('nama')->heading('Nama Pemain'),
                                    Column::make('umur')->heading('Umur'),
                                    Column::make('umur_kategori')->heading('Kategori Umur'),
                                    Column::make('sekolahBola.nama')->heading('Sekolah Bola'),
                                    Column::make('sekolahBola.pic')->heading('PIC Sekolah'),
                                    Column::make('sekolahBola.telepon')->heading('Telepon Sekolah'),
                                    Column::make('created_at')
                                        ->heading('Tanggal Daftar')
                                        ->formatStateUsing(fn ($state) => $state?->format('d/m/Y')),
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
                            ->withFilename(fn () => 'semua-data-pemain-bola-' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->withColumns([
                                Column::make('nama')->heading('Nama Pemain'),
                                Column::make('umur')->heading('Umur'),
                                Column::make('umur_kategori')->heading('Kategori Umur'),
                                Column::make('sekolahBola.nama')->heading('Sekolah Bola'),
                                Column::make('sekolahBola.pic')->heading('PIC Sekolah'),
                                Column::make('sekolahBola.telepon')->heading('Telepon Sekolah'),
                                Column::make('created_at')
                                    ->heading('Tanggal Daftar')
                                    ->formatStateUsing(fn ($state) => $state?->format('d/m/Y')),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemainBolas::route('/'),
            'create' => Pages\CreatePemainBola::route('/create'),
            'edit' => Pages\EditPemainBola::route('/{record}/edit'),
        ];
    }
}
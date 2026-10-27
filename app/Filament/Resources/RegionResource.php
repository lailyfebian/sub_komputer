<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionResource\Pages;
use App\Filament\Resources\RegionResource\RelationManagers;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RegionImport;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Wilayah';

    public static function getNavigationSort(): ?int
    {
        return 1; 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Wilayah')
                    ->required()
                    ->reactive(),

                Forms\Components\Select::make('type')
                    ->label('Tipe Wilayah')
                    ->options([
                        'kota' => 'Kota / Kabupaten',
                        'kecamatan' => 'Kecamatan',
                        'kelurahan' => 'Kelurahan',
                        'desa' => 'Desa',
                    ])
                    ->required(),

                Forms\Components\Select::make('parent_id')
                    ->label('Parent Region')
                    ->options(function (callable $get) {
                        $type = $get('type');

                        if (!$type) return [];

                        return match ($type) {
                            // kota tidak punya parent
                            'kota' => [],

                            // kecamatan -> list semua kota
                            'kecamatan' => Region::where('type', 'kota')
                                ->orderBy('name')
                                ->pluck('name', 'id'),

                            // kelurahan / desa -> list semua kecamatan dengan format: "Kecamatan - Kota"
                            'kelurahan', 'desa' => Region::where('type', 'kecamatan')
                                ->with('parent') // parent di sini adalah kota
                                ->get()
                                ->mapWithKeys(function ($region) {
                                    $cityName = $region->parent?->name ?? '-';
                                    return [
                                        $region->id => "{$region->name} - {$cityName}"
                                    ];
                                }),

                            default => [],
                        };
                    })
                    ->searchable()
                    ->reactive()
                    ->disabled(fn(callable $get) => $get('type') === 'kota')
                    ->nullable(),



                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->disabled()
                    ->dehydrated(false)
                    ->reactive()
                    ->afterStateHydrated(fn($set, $record) =>
                        $set('slug', $record?->slug)
                    ),

                Forms\Components\TextArea::make('address')
                    ->label('Alamat Wilayah')
                    ->placeholder('Alamat Wilayah'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama'),
                Tables\Columns\TextColumn::make('type')->label('Tipe'),
                Tables\Columns\TextColumn::make('slug')->label('Slug'),
                Tables\Columns\TextColumn::make('parent.name')->label('Parent'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make(), // ✅ Export semua data

                Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Pilih File Excel (.xlsx)')
                            ->required()
                            ->storeFiles(false)
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ]),
                    ])
                    ->action(function (array $data) {
                        Excel::import(new RegionImport, $data['file']);
                    })
                    ->successNotificationTitle('Import berhasil!'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}

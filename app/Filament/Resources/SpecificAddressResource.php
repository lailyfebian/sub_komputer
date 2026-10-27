<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecificAddressResource\Pages;
use App\Filament\Resources\SpecificAddressResource\RelationManagers;
use App\Models\specificAddresses;
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
use App\Imports\SpecificAddressImport;

class SpecificAddressResource extends Resource
{
    protected static ?string $model = specificAddresses::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Alamat Sekolah';
    public static function getNavigationSort(): ?int
    {
        return 2; 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('region_id')
                    ->label('Wilayah')
                    ->relationship(
                        name: 'region',
                        titleAttribute: 'name'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'sd_negeri' => 'SD Negeri',
                        'smp_negeri' => 'SMP Negeri',
                        'sma_negeri' => 'SMA Negeri',
                        'sd_swasta' => 'SD Swasta',
                        'smp_swasta' => 'SMP Swasta',
                        'sma_swasta' => 'SMA Swasta',
                        'mts' => 'MTs Negeri & Swasta',
                        'ma' => 'MA Negeri & Swasta',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('region.name')
                    ->label('Wilayah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('region.type')
                    ->label('Tipe Wilayah')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'kota' => 'primary',
                        'kecamatan' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'sd_negeri' => 'SD Negeri',
                        'smp_negeri' => 'SMP Negeri',
                        'sma_negeri' => 'SMA Negeri',
                        'sd_swasta' => 'SD Swasta',
                        'smp_swasta' => 'SMP Swasta',
                        'sma_swasta' => 'SMA Swasta',
                        'mts' => 'MTs Negeri & Swasta',
                        'ma' => 'MA Negeri & Swasta',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('school_addresses_count')
                    ->label('Jumlah Sekolah')
                    ->counts('schoolAddresses')
                    ->badge()
                    ->formatStateUsing(fn ($state) => "{$state} / 5"),

                Tables\Columns\TextColumn::make('schoolAddresses.school_name')
                    ->label('Sekolah')
                    ->listWithLineBreaks()
                    ->limitList(5),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region_type')
                    ->label('Tipe Wilayah')
                    ->options([
                        'kota' => 'Kota',
                        'kecamatan' => 'Kecamatan',
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when(
                            $data['value'] ?? null,
                            fn ($query, $value) =>
                                $query->whereHas(
                                    'region',
                                    fn ($q) => $q->where('type', $value)
                                )
                        );
                    }),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'sd_negeri' => 'SD Negeri',
                        'smp_negeri' => 'SMP Negeri',
                        'sma_negeri' => 'SMA Negeri',
                        'sd_swasta' => 'SD Swasta',
                        'smp_swasta' => 'SMP Swasta',
                        'sma_swasta' => 'SMA Swasta',
                        'mts' => 'MTs Negeri & Swasta',
                        'ma' => 'MA Negeri & Swasta',
                    ]),
            ])
            ->headerActions([
                ExportAction::make(),

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
                        Excel::import(
                            new SpecificAddressImport,
                            $data['file']
                        );
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
            RelationManagers\SchoolAddressesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpecificAddresses::route('/'),
            'create' => Pages\CreateSpecificAddress::route('/create'),
            'edit' => Pages\EditSpecificAddress::route('/{record}/edit'),
        ];
    }
}

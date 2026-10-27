<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KeywordTemplateResource\Pages;
use App\Models\KeywordTemplate;
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
use App\Imports\KeywordTemplateImport;

class KeywordTemplateResource extends Resource
{
    protected static ?string $model = KeywordTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationLabel = 'Template Kata Kunci';

    public static function getNavigationSort(): ?int
    {
        return 3; 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Keyword Template')
                            ->placeholder('Keyword Template')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state, $get) {
                                if (blank($get('slug'))) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->placeholder('Slug')
                            ->helperText('Biarkan kosong agar otomatis dibuat dari keyword.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Keyword Template')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug'),
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
                        Excel::import(new KeywordTemplateImport, $data['file']);
                    })
                    ->successNotificationTitle('Import berhasil!'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Data')
                    ->modalSubheading('Yakin ingin menghapus data ini?')
                    ->modalButton('Ya, Hapus')
                    ->successNotificationTitle('Data berhasil dihapus!'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKeywordTemplates::route('/'),
            'create' => Pages\CreateKeywordTemplate::route('/create'),
            'edit' => Pages\EditKeywordTemplate::route('/{record}/edit'),
        ];
    }
}

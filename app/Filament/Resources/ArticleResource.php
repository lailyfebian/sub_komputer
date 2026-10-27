<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Actions\Action;
use Filament\Forms\Set;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ArticleImport;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function getNavigationSort(): ?int
    {
        return 4; 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            FileUpload::make('image')
                ->directory('articles') // folder storage/app/public/articles
                ->image()
                ->maxSize(2048) // 2MB
                ->nullable()
                ->label('Foto Artikel'),
                // Judul halaman
            Forms\Components\TextInput::make('title')
                ->label('Judul Halaman')
                ->required()
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state, callable $get) {
                    $currentSlug = $get('slug');
                    if (empty($currentSlug) || $currentSlug === Str::slug($get('title') ?? '')) {
                        $set('slug', static::generateUniqueSlug($state));
                    }
                }),

            // Slug (auto-generate jika kosong)
            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->helperText('Slug akan otomatis dibuat dari judul jika dikosongkan.')
                ->reactive()
                ->afterStateHydrated(function ($set, $record) {
                    if ($record && empty($record->slug)) {
                        $set('slug', static::generateUniqueSlug($record->title));
                    }
                })
                ->dehydrated(true),

            // REPEATER UNTUK SECTION HALAMAN
            Forms\Components\Repeater::make('data')
            ->label('Section Halaman')
            ->schema([

                Forms\Components\Select::make('type')
                    ->label('Tipe Section')
                    ->options([
                        'content' => 'Konten Artikel',
                    ])
                    ->required()
                    ->reactive(),

                // === CONTENT ===
                Forms\Components\RichEditor::make('content')
                    ->label('Isi Konten')
                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                    ->visible(fn ($get) => in_array($get('type'), [
                        'content',
                    ])),
            ])
            ->collapsible()
            ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->square()
                    ->height(60),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('clone')
                    ->label('Clone')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function ($record) {
                        $new = $record->replicate(); // clone data
                        $new->slug = Str::slug($record->title . '-' . now()->timestamp);
                        $new->save();

                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil meng-clone data!')
                            ->success()
                            ->send();
                    }),
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
                    Excel::import(new ArticleImport, $data['file']);
                })
                ->successNotificationTitle('Import berhasil!'),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    /**
     * Generate unique slug from title.
     */
    public static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 2;

        while (Article::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        if ($slug) {
            return $slug;
        } else {
            return Str::random(8);
        }
    }
}

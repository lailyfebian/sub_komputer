<?php

namespace App\Filament\Resources\KeywordTemplateResource\Pages;

use App\Filament\Resources\KeywordTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKeywordTemplates extends ListRecords
{
    protected static string $resource = KeywordTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Kata Kunci';
    }
}

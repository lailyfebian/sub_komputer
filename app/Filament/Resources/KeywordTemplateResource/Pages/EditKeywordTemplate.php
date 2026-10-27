<?php

namespace App\Filament\Resources\KeywordTemplateResource\Pages;

use App\Filament\Resources\KeywordTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKeywordTemplate extends EditRecord
{
    protected static string $resource = KeywordTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Edit Kata Kunci';
    }
}

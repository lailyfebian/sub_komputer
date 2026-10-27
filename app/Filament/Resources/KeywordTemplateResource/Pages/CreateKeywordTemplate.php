<?php

namespace App\Filament\Resources\KeywordTemplateResource\Pages;

use App\Filament\Resources\KeywordTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKeywordTemplate extends CreateRecord
{
    protected static string $resource = KeywordTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Buat Kata Kunci Baru';
    }
}

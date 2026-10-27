<?php

namespace App\Filament\Resources\SpecificAddressResource\Pages;

use App\Filament\Resources\SpecificAddressResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpecificAddresses extends ListRecords
{
    protected static string $resource = SpecificAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'List Alamat Sekolah';
    }
}

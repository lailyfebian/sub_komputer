<?php

namespace App\Filament\Resources\SpecificAddressResource\Pages;

use App\Filament\Resources\SpecificAddressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpecificAddress extends EditRecord
{
    protected static string $resource = SpecificAddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Alamat Sekolah';
    }
}

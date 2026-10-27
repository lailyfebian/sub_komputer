<?php

namespace App\Filament\Resources\SpecificAddressResource\Pages;

use App\Filament\Resources\SpecificAddressResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecificAddress extends CreateRecord
{
    protected static string $resource = SpecificAddressResource::class;

    public function getTitle(): string
    {
        return 'Buat Alamat Sekolah';
    }
}

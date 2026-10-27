<?php

namespace App\Filament\Resources\SpecificAddressResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SchoolAddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'schoolAddresses';

    protected static ?string $title = 'Daftar Alamat Sekolah';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('school_name')
                    ->label('Nama Sekolah')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\Textarea::make('address')
                    ->label('Alamat')
                    ->required()
                    ->rows(4),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('school_name')
            ->columns([
                Tables\Columns\TextColumn::make('school_name')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Alamat'),
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
}
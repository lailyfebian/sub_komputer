<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Region;
use App\Models\KeywordTemplate;
use App\Models\PageContent;
use App\Models\Article;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Halaman Konten Website', PageContent::count())
                ->description('Jumlah semua halaman konten yang terdaftar')
                ->icon('heroicon-o-document-text'),
                
            Card::make('Total Kata Kunci', KeywordTemplate::count())
                ->description('Jumlah semua kata kunci yang terdaftar')
                ->icon('heroicon-o-folder'),
                
            Card::make('Total Kota', Region::count())
                ->description('Jumlah semua wilayah yang terdaftar')
                ->icon('heroicon-o-building-office'),
                
            Card::make('Total Artikel', Article::count())
                ->description('Jumlah artikel yang terdaftar')
                ->icon('heroicon-o-document-text'),
        ];
    }

    protected function getColumns(): int
    {
        return 2; // membuat layout 2 card per baris
    }
}

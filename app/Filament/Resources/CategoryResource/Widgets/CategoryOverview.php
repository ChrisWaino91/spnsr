<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CategoryOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Impressions', '0')
                ->description('The number of views of all ads in this category.')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1,23,34,47,58,90,114,190])
                ->color('primary'),
            Stat::make('Clicks', '0')
                ->description('The number of clicks these ads have generated.')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1,23,34,47,58,90,114,190])
                ->color('primary'),
            Stat::make('Conversion Rate', '0%')
                ->description('The percentage of clicks that have resulted in an order.')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1,23,34,47,58,90,114,190])
                ->color('success'),
        ];
    }
}

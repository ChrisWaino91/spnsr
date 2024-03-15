<?php

namespace App\Filament\Resources\PromotionResource\Widgets;

use Illuminate\Database\Eloquent\Model;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PromotionOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Impressions', number_format($this->record->impressions->count()))
                ->description('The number of views of all ads in this category.')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1,23,34,47,58,90,114,190])
                ->color('primary'),
            Stat::make('Clicks', number_format($this->record->clicks->count()))
                ->description('The number of clicks these ads have generated.')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1,23,34,47,58,90,114,190])
                ->color('primary'),
            Stat::make('Orders', $this->record->orders->count())
                ->description('The number of orders placed from this promotion.')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1,23,34,47,58,90,114,190])
                ->color('success'),
        ];
    }
}

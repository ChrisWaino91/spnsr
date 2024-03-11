<?php

namespace App\Filament\Resources\CampaignResource\Widgets;

use Illuminate\Database\Eloquent\Model;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class CampaignOverview extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Impressions', number_format($this->record->total_impressions))
                ->description('The number of views of all ads in this category.')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1,23,34,47,58,90,114,190])
                ->color('primary'),
            Stat::make('Clicks', number_format($this->record->total_clicks))
                ->description('The number of clicks these ads have generated.')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1,23,34,47,58,90,114,190])
                ->color('primary'),
            Stat::make('Total Orders', number_format($this->record->total_orders))
                ->description('The amount of orders this campaign has produced.')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([1,23,34,47,58,90,114,190])
                ->color('success'),
        ];
    }
}

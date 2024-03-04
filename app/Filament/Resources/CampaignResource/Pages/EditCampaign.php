<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CampaignResource;
use App\Filament\Resources\CampaignResource\Widgets\CampaignOverview;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CampaignOverview::class,
        ];
    }
}

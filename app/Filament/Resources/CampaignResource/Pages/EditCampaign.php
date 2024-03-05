<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CampaignResource;
use App\Filament\Resources\CampaignResource\Widgets\CampaignOverview;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    // todo, extract these somewhere else
    protected function beforeSave(): void
    {
        if (
            $this->data['active'] &&
            $this->record->spend() >= $this->data['budget']
        ) {
            Notification::make()
                ->title('This campaign has already exceeded it\'s budget and cannot be activated. You must increase the budget if you wish to proceed.')
                ->danger()
                ->send();

            $this->halt();
        }

        if (
            $this->data['active'] &&
            $this->data['end_date'] < now()
        ) {
            Notification::make()
                ->title('This campaign has an end date in the past. Please adjust if you wish to proceed.')
                ->danger()
                ->send();

            $this->halt();
        }

    }

    public function getTitle(): string
    {
        return $this->record->name;
    }

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

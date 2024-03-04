<?php

namespace App\Filament\Resources\PromotionResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PromotionResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\PromotionResource\Widgets\PromotionOverview;

class EditPromotion extends EditRecord
{
    protected static string $resource = PromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PromotionOverview::class,
        ];
    }
}

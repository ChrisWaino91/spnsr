<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    public function getTitle(): string
    {
        return $this->record->title;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['price'] = number_format($data['price'], 2);
        $data['sale_price'] = number_format($data['sale_price'], 2);
        $data['rrp_price'] = number_format($data['rrp_price'], 2);

        return $data;
    }
}

<?php

namespace App\Filament\Resources\BrandResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\BrandResource;

class ViewBrand extends ViewRecord
{
    protected static string $resource = BrandResource::class;

    public function getTitle(): string
    {
        return $this->record->name;
    }

}

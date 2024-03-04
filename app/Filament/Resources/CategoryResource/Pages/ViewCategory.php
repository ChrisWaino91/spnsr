<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CategoryResource\Widgets\CategoryOverview;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    public function getTitle(): string
    {
        return $this->record->name;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CategoryOverview::class,
        ];
    }

}

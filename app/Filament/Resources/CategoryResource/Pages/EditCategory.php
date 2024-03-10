<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CategoryResource\Widgets\CategoryOverview;

class EditCategory extends EditRecord
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

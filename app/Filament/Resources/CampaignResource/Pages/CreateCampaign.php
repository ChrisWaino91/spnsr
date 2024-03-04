<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CampaignResource;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (Auth::user()->hasRole('admin')) {
            $data['supplier_id'] = $data['supplier_id'];
        } else {
            // todo, handle an input for this if they have more than one supplier
            $data['supplier_id'] = Auth::user()->suppliers()->first()->id;
        }

        return $data;
    }
}

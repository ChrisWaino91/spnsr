<?php

namespace App\Filament\Resources\PromotionResource\Pages;

use Filament\Actions;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PromotionResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\PromotionResource\Widgets\PromotionOverview;

class EditPromotion extends EditRecord
{
    protected static string $resource = PromotionResource::class;

    public function getTitle(): string
    {
        return $this->record->category->name . ' Promotion';
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
            PromotionOverview::class,
        ];
    }

    // todo, extrac these somewhere else
    protected function beforeSave(): void
    {
        if (
            $this->data['active'] &&
            !$this->record->active &&
            Category::where('id', $this->data['category_id'])
                ->where('promotion_id', '<>', 0)
                ->exists()
        ) {
            Notification::make()
                ->title('Sorry this promotion could not be saved at this time as there is already an active promotion for this category.')
                ->danger()
                ->send();

            $this->halt();
        }

        if (
            $this->data['active'] &&
            $this->record->spend() >= $this->record->budget
        ) {
            Notification::make()
                ->title('This promotion has already exceeded it\'s budget and cannot be activated. You must increase the budget if you wish to proceed.')
                ->danger()
                ->send();

            $this->halt();
        }

    }

    protected function afterSave(): void
    {
        $category = Category::find($this->record->category_id);

        if ($this->record->active) {
            $category->update([
                'promotion_id' => $this->record->id,
            ]);
        } elseif (!$this->record->active) {
            $category->update([
                'promotion_id' => 0,
            ]);
            Notification::make()
                ->title('Saved. Please note that this category - ' . $category->name . ' - is now free to be promoted by another promotion as this one is now inactive.')
                ->warning()
                ->send();
        }
    }

    protected function afterCreate(): void
    {
        $category = Category::find($this->record->category_id);
        $category->update([
            'promotion_id' => $this->record->id,
        ]);
    }
}

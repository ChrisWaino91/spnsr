<?php

namespace App\Filament\Resources\CampaignResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PromotionResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PromotionsRelationManager extends RelationManager
{
    protected static string $relationship = 'promotions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('campaign_id')
                    ->relationship('campaign', 'name')
                    ->default($this->getOwnerRecord()->id)
                    ->disabled()
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(function() {
                        return Category::where('promotion_id', 0)->pluck('name', 'id')->toArray();
                    })
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $costPerClick = Category::where('id', $state)->value('cost_per_click');
                        $set('cost_per_click', $costPerClick);
                    }),
                Forms\Components\TextInput::make('cost_per_click')
                    ->required()
                    ->prefix('£')
                    ->readonly(),
                Forms\Components\TextInput::make('budget')
                    ->prefix('£')
                    ->required(),
                Forms\Components\MultiSelect::make('product')
                    ->label('Products')
                    ->relationship('products', 'title')
                    ->options(function (Get $get) {
                        $userId = Auth::id();
                        $categoryId = $get('category_id');
                        $products = Product::query()
                            ->whereHas('categories', function (Builder $query) use ($categoryId) {
                                $query->where('categories.id', $categoryId);
                            })
                            ->whereHas('brand.supplier.users', function (Builder $query) use ($userId) {
                                $query->where('users.id', $userId);
                            })
                            ->pluck('title', 'id');

                        return $products->toArray();
                    })
                    ->preload()
                    ->columnSpan('full'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->description('A campaign can consist of multiple promotions. A promotion is set against a single specific category, for which you can assign multiple products to promote.')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('cost_per_click')
                    ->label('Cost Per Click')
                    ->money('GBP'),
                Tables\Columns\TextColumn::make('budget')
                    ->money('GBP')
                    ->label('Budget'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        return $record->active ? 'Active' : 'Disabled';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Disabled' => 'danger',
                    })
                    ->sortable(),

            ])
            ->recordUrl(
                function (Model $record): string {
                    return $record->url();
                },
            )
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('Edit')
                    ->url(fn ($record) => PromotionResource::getUrl('edit', ['record' => $record->id]))
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),
            ])
            ->bulkActions([

            ]);
    }
}

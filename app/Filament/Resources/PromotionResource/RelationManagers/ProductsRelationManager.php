<?php

namespace App\Filament\Resources\PromotionResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Click;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Form;
use App\Models\Impression;
use Filament\Tables\Table;
use App\Models\ProductPromotion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'Products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading('Promoted Products')
            ->description('Here are the products that you\'ve chosen to promote against this category.')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('impressions')
                    ->getStateUsing(function ($record) {
                        return Impression::where([
                            'product_id' => $record->id,
                            'promotion_id' => $this->ownerRecord->id,
                        ])
                        ->count();
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('clicks')
                    ->getStateUsing(function ($record) {
                        return Click::where([
                            'product_id' => $record->id,
                            'promotion_id' => $this->ownerRecord->id,
                        ])
                        ->count();
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('orders')
                    ->getStateUsing(function ($record) {
                        return 0;
                })
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $product = ProductPromotion::where([
                            'product_id' => $record->id,
                            'promotion_id' => $this->ownerRecord->id,
                        ])->withTrashed()
                        ->first();

                        return !$product->deleted_at ? 'Active' : 'Stopped';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Stopped' => 'danger',
                    })
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->model(ProductPromotion::class)
                    ->form([
                        Forms\Components\MultiSelect::make('product')
                            ->label('Products')
                            ->relationship('products', 'title')
                            ->options(function () {
                                $userId = Auth::id();
                                $promotion = $this->ownerRecord;
                                $category = $promotion->category;

                                $products = Product::query()
                                    ->whereHas('categories', function (Builder $query) use ($category) {
                                        $query->where('categories.id', $category->id);
                                    })
                                    ->whereHas('brand.supplier.users', function (Builder $query) use ($userId) {
                                        $query->where('users.id', $userId);
                                    })
                                    ->whereDoesntHave('promotions', function (Builder $query) use ($promotion) {
                                        $query->where('promotions.id', $promotion->id);
                                    })
                                    ->pluck('title', 'id');

                                return $products->toArray();
                            })
                            ->preload()
                            ->columnSpan('full'),
                    ])
                    ->action(function (array $data, Tables\Actions\CreateAction $action) {
                        $promotionId = $this->ownerRecord->id;

                        foreach ($this->mountedTableActionsData[0]['product'] as $productId) {
                            ProductPromotion::firstOrCreate([
                                'product_id' => $productId,
                                'promotion_id' => $promotionId,
                            ]);
                        }

                        $action->success('Products have been successfully added to the promotion.');
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('stop')
                    ->visible(function ($record) {
                        $product = ProductPromotion::where([
                            'product_id' => $record->id,
                            'promotion_id' => $this->ownerRecord->id,
                        ])->withTrashed()
                        ->first();

                        return !$product->deleted_at;
                    })
                    ->label('Stop')
                    ->action(function ($record) {
                        ProductPromotion::where([
                            'product_id' => $record->id,
                            'promotion_id' => $this->ownerRecord->id,
                        ])->delete();
                    })
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Stop Promoting This Product?')
                    ->modalDescription('Are you sure you\'d like to stop promoting this product? You can always undo this action later.')
                    ->modalSubmitActionLabel('Yes'),
            Tables\Actions\Action::make('start')
                    ->visible(function ($record) {
                        $product = ProductPromotion::where([
                            'product_id' => $record->id,
                            'promotion_id' => $this->ownerRecord->id,
                        ])->withTrashed()
                        ->first();

                        return $product->deleted_at;
                    })
                    ->label('Resume')
                    ->action(function ($record) {
                        ProductPromotion::where([
                            'product_id' => $record->id,
                            'promotion_id' => $this->ownerRecord->id,
                        ])->restore();
                    })
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Start Promoting This Product Again?')
                    ->modalDescription('Are you sure you\'d like to begin promoting this product again? You can always undo this action later.')
                    ->modalSubmitActionLabel('Yes'),
            ]);
    }
}

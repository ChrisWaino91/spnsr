<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Promotion;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\PromotionResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PromotionResource\RelationManagers;
use App\Filament\Resources\PromotionResource\Widgets\PromotionOverview;
use App\Filament\Resources\PromotionResource\RelationManagers\ProductsRelationManager;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static bool $shouldRegisterNavigation= false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Promotion Details')
                    ->description('Here is where you configure the specific ads you want to run against a category. Remember you can run as many promotions as you wish within a campaign.')
                    ->schema([
                        Forms\Components\Select::make('campaign_id')
                            ->relationship('campaign', 'name')
                            ->disabled()
                            ->required(),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('cost_per_click')
                            ->prefix('£')
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('budget')
                            ->prefix('£')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('spend')
                            ->label('Current Spend')
                            ->disabled()
                            ->placeholder(function ($record) {
                                return $record->spend();
                            })
                            ->prefix('£'),
                        Forms\Components\Toggle::make('active')
                            ->onColor('success')
                            ->offColor('danger')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('admin')) {
                    return;
                }

                $userId = auth()->id();

                $query->whereHas('campaign.supplier.users', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('campaign.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_per_click')
                    ->money('GBP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('budget')
                    ->money('GBP')
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('id')
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromotions::route('/'),
            'create' => Pages\CreatePromotion::route('/create'),
            'edit' => Pages\EditPromotion::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            PromotionOverview::class,
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getNavigationGroup(): string
    {
        return 'Shop';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('api_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('£'),
                Forms\Components\TextInput::make('sale_price')
                    ->required()
                    ->numeric()
                    ->prefix('£'),
                Forms\Components\TextInput::make('rrp_price')
                    ->required()
                    ->numeric()
                    ->prefix('£'),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        // hide brand column if user is not admin
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('admin')) {
                    return;
                }

                $userId = auth()->id();

                $query->whereHas('brand.supplier.users', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                });

            })
            ->columns([
                Tables\Columns\ImageColumn::make('images.thumbnail')
                    ->label('#')
                    ->circular(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable( isToggledHiddenByDefault: !auth()->user()->hasRole('admin')),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('GBP')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->money('GBP')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('rrp_price')
                    ->money('GBP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
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

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'view' => Pages\ViewProduct::route('/{record}/view'),
        ];
    }

}

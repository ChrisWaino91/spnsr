<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components\Group;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationGroup(): string
    {
        return 'Shop';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\ImageColumn::make('thumbnail_image_url')
                    ->label('#')
                    ->circular(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable( isToggledHiddenByDefault: !auth()->user()->hasRole('admin')),
                Tables\Columns\TextColumn::make('title')
                    ->limit(40)
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Product Information')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('medium_image_url')
                            ->label('')
                            ->width(450)
                            ->height(600),
                        Group::make()
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title:'),
                                TextEntry::make('brand.name')
                                    ->label('Brand:'),
                                TextEntry::make('reference')
                                    ->label('Reference:'),
                                TextEntry::make('price')
                                    ->label('Price:')
                                    ->prefix('£')
                                    ->numeric(decimalPlaces: 2),
                                TextEntry::make('sale_price')
                                    ->label('Sale Price:')
                                    ->prefix('£')
                                    ->numeric(decimalPlaces: 2),
                                TextEntry::make('rrp_price')
                                    ->label('RRP Price:')
                                    ->prefix('£')
                                    ->numeric(decimalPlaces: 2),
                                TextEntry::make('stock')
                                    ->label('Stock:'),


                            ])
                        ]),
                Section::make('Category Information')
                    ->schema([
                        TextEntry::make('categories.name')
                            ->label('Categories')
                            ->listWithLineBreaks()
                    ]),

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

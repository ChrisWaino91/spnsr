<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use App\Models\Campaign;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use RelationManagers\PromotionManager;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CampaignResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CampaignResource\RelationManagers;
use App\Filament\Resources\CampaignResource\Widgets\CampaignOverview;
use App\Filament\Resources\CampaignResource\RelationManagers\PromotionsRelationManager;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';


    public static function getNavigationBadge(): ?string
    {
        $userId = Auth::id();

        $campaignCount = Campaign::whereHas('supplier.users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->count();

        return $campaignCount;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function getNavigationGroup(): string
    {
        return 'Ads';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Campaign Details')
                    ->description('Enter the details of the campaign here.')
                    ->schema([
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->visible(auth()->user()->hasRole('admin'))
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('start_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->required()
                            ->default(now()->addMonth(1)),
                        Forms\Components\TextInput::make('budget')
                            ->hint('You can always change this later.')
                            ->required()
                            ->prefix('£')
                            ->numeric(),
                        Forms\Components\Toggle::make('active')
                            ->onColor('success')
                            ->offColor('danger')
                    ])->columns(2)
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
                $query->whereHas('supplier.users', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable()
                    ->visible(auth()->user()->isAdmin()),
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->dateTime()
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            'promotions' => PromotionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            CampaignOverview::class,
        ];
    }
}

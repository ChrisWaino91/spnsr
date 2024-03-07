<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use App\Models\Campaign;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\InvoiceResource\Pages;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InvoiceResource\RelationManagers;

class InvoiceResource extends Resource
{
protected static ?string $model = Invoice::class;

protected static ?string $navigationIcon = 'heroicon-o-calculator';

public static function getNavigationGroup(): string
{
    return 'Reports';
}

public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Section::make(function ($record) {
            return 'Invoice Ref #' . $record->id;
        })
            ->description(function ($record) {
                if ($record->finalised) {
                    return 'This invoice has been finalised and is ready for payment.';
                } else {
                    return 'This is a live invoice and has not yet been finalised. This will updated overnight and finalised at the end of a given month.';
                }
            })
            ->schema([
                TextInput::make('total_amount')
                    ->prefix('£')
                    ->label('Total Amount Due:')
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
            $query->whereHas('supplier.users', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            });
        })
        ->columns([
            Tables\Columns\TextColumn::make('total_amount')
                ->numeric()
                ->money('GBP')
                ->sortable(),
            Tables\Columns\TextColumn::make('invoice_date')
                ->date('M, Y')
                ->sortable(),
            Tables\Columns\IconColumn::make('finalised')
                ->tooltip('Once the month has ended, the invoice will be finalised and ready for you to pay.')
                ->boolean(),
            Tables\Columns\IconColumn::make('paid')
                ->boolean(),
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
            Tables\Actions\ViewAction::make(),
        ])
        ->bulkActions([

        ]);
}

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make(function ($record) {
                return 'Invoice Ref #' . $record->id;
            })
                ->description(function ($record) {
                    if ($record->finalised) {
                        return 'This invoice has been finalised and is ready for payment.';
                    } else {
                        return 'This is a live invoice and has not yet been finalised. This will be updated overnight and finalised at the end of a given month.';
                    }
                })
                ->schema([
                    TextEntry::make('total_amount')
                        ->label('Total Amount Due:')
                        ->badge()
                        ->color('success')
                        ->money('GBP'),
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
        'index' => Pages\ListInvoices::route('/'),
        'view' => Pages\ViewInvoice::route('/{record}'),

    ];
}
}

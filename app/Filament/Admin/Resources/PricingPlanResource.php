<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PricingPlanResource\Pages;
use App\Models\PricingPlan;
use Filament\Forms\Form;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;


class PricingPlanResource extends Resource
{
    protected static ?string $model = PricingPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Website Settings';
    protected static ?string $navigationLabel = 'Pricing Plans';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('name')
                        ->label('Plan Name')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, $set) =>
                            $set('slug', \Str::slug($state))
                        ),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true),

                    TextInput::make('monthly_price')
                        ->label('Monthly Price')
                        ->numeric()
                        ->required(),

                    TextInput::make('yearly_price')
                        ->label('Yearly Price')
                        ->numeric()
                        ->required(),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(3),

                    Toggle::make('is_popular')
                        ->label('Popular Plan'),

                    Repeater::make('features')
                        ->label('Features')
                        ->schema([
                            TextInput::make('feature')
                                ->label('Feature')
                                ->required(),
                        ])
                        ->minItems(1)
                        ->columnSpanFull(),

                    TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0),

                    Toggle::make('status')
                        ->label('Active')
                        ->default(true),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Plan Name')->sortable()->searchable(),
                TextColumn::make('slug')->label('Slug')->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(10)
                    ->tooltip(fn ($state) => $state)
                    ->sortable(),
                TextColumn::make('monthly_price')->label('Monthly Price')->money('bdt'),
                TextColumn::make('yearly_price')->label('Yearly Price')->money('bdt'),


  
                TextColumn::make('features')
                    ->label('Features')
                    ->limit(10)
                    ->tooltip(fn ($record) =>
                        collect($record->features)->pluck('feature')->implode(', '))
                    ->formatStateUsing(function ($state, $record) {
                        // Directly take data from model cast (always array)
                        $features = $record->features;

                        if (!$features || !is_array($features)) {
                            return '-';
                        }

                        return implode(', ', array_column($features, 'feature'));
                    }),


                IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean(),

                IconColumn::make('status')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->sortable(),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                // Plan Information Section
                Section::make('Plan Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Plan Name')
                            ->color('primary'),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->color('secondary'),

                        TextEntry::make('monthly_price')
                            ->label('Monthly Price')
                            ->color('success'),

                        TextEntry::make('yearly_price')
                            ->label('Yearly Price')
                            ->color('success'),

                        TextEntry::make('description')
                            ->label('Description')
                            ->color('gray'),

                        TextEntry::make('is_popular')
                            ->label('Popular')
                            ->color(fn($state) => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No'),

                        TextEntry::make('status')
                            ->label('Active')
                            ->color(fn($state) => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive'),
                    ])
                    ->columns(2),

                // Features Section
                Section::make('Features')
                    ->schema([
                        RepeatableEntry::make('features')
                            ->schema([
                                TextEntry::make('feature')
                                    ->label('Feature')
                                    ->color('primary'),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

            ]);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPricingPlans::route('/'),
            'create' => Pages\CreatePricingPlan::route('/create'),
            'edit' => Pages\EditPricingPlan::route('/{record}/edit'),
        ];
    }
}

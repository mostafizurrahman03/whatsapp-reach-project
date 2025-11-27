<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeatureResource\Pages;
use App\Models\Feature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;


class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Website Settings';
    protected static ?string $navigationLabel = 'Features';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->columnSpan('full')
                    ->schema([

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Feature Title')
                                    ->placeholder('Bulk Messaging')
                                    ->required()
                                    ->maxLength(150),

                                Forms\Components\TextInput::make('icon')
                                    ->label('Icon / Emoji')
                                    ->placeholder('💬 or heroicon-o-chat-bubble-left')
                                    ->helperText('You can use emoji or any icon class')
                                    ->maxLength(100),
                            ]),

                        Forms\Components\Textarea::make('short_description')
                            ->label('Short Description')
                            ->placeholder('Add a small description about this feature...')
                            ->rows(3)
                            ->autosize()
                            ->nullable(),

                        Forms\Components\Repeater::make('items')
                            ->label('Feature Bullet Points')
                            ->schema([
                                Forms\Components\TextInput::make('value')
                                    ->label('Point')
                                    ->placeholder('CSV import with preview')
                                    ->required(),
                            ])
                            ->default([])
                            ->columns(1)
                            ->addActionLabel('Add New Point')
                            ->reorderable(true),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),

                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Feature Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('short_description')
                    ->label('Description')
                    ->limit(15)
                    ->tooltip(fn ($record) => 
                        collect($record->items)->pluck('value')->implode(', ') ?: '-'
                    ),
                Tables\Columns\TextColumn::make('items')
                    ->label('Bullet Points')
                    ->limit(15)
                    ->tooltip(fn ($record) => 
                        collect($record->items)->pluck('value')->implode(', ') ?: '-'
                    )
                    ->formatStateUsing(function ($state, $record) {
                        $items = $record->items;

                        if (!is_array($items) || empty($items)) {
                            return '-';
                        }

                        // pluck only the 'value' field and implode to string
                        return collect($items)->pluck('value')->implode(', ');
                    }),


                Tables\Columns\TextColumn::make('icon')
                    ->label('Icon')
                    ->limit(15)
                    ->tooltip(fn ($record) => $record->icon ?: '-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active?'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('Active'),
                    
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Feature Details')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Feature Title')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('icon')
                            ->label('Icon')
                            ->formatStateUsing(fn ($state) => $state ?: '—'),

                        TextEntry::make('short_description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->placeholder('No description provided.')
                            ->markdown(),

                        KeyValueEntry::make('items')
                            ->label('Bullet Points')
                            ->keyLabel('Index')
                            ->valueLabel('Point')
                            ->columns(1)
                            ->columnSpanFull()
                            ->getStateUsing(function ($record) {
                                $items = $record->items;

                                if (!is_array($items) || empty($items)) {
                                    return [];
                                }

                                // Convert array of objects to key => value
                                return collect($items)->mapWithKeys(function ($item, $index) {
                                    return [$index + 1 => $item['value'] ?? '-'];
                                })->toArray();
                            })
                            ->placeholder('No items added'),


                        IconEntry::make('is_active')
                            ->label('Active Status')
                            ->boolean(),

                        TextEntry::make('sort_order')
                            ->label('Sort Order'),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])
                    ->columns(2)
            ]);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeatures::route('/'),
            'create' => Pages\CreateFeature::route('/create'),
            'edit' => Pages\EditFeature::route('/{record}/edit'),
        ];
    }
}

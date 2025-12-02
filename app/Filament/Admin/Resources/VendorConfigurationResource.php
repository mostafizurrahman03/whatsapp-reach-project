<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VendorConfigurationResource\Pages;
use App\Filament\Admin\Resources\VendorConfigurationResource\RelationManagers;
use App\Models\VendorConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorConfigurationResource extends Resource
{
    protected static ?string $model = VendorConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Service Settings';
    protected static ?string $navigationLabel = 'Vendor Configurations';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('service_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('vendor_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('api_key')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('secret_key')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('base_url')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('tps')
                    ->required()
                    ->numeric()
                    ->default(10),
                Forms\Components\Textarea::make('extra_config')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendor_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('api_key')
                    ->searchable(),
                Tables\Columns\TextColumn::make('secret_key')
                    ->searchable(),
                Tables\Columns\TextColumn::make('base_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tps')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendorConfigurations::route('/'),
            'create' => Pages\CreateVendorConfiguration::route('/create'),
            'edit' => Pages\EditVendorConfiguration::route('/{record}/edit'),
        ];
    }
}

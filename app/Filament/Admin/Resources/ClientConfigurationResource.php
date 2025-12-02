<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ClientConfigurationResource\Pages;
use App\Models\ClientConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentJsonEditor\Forms\Components\JsonEditor;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Forms\Components\KeyValue;

class ClientConfigurationResource extends Resource
{
    protected static ?string $model = ClientConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Messaging Gateway';
    protected static ?string $navigationLabel = 'Client Configurations';

    public static function getNavigationBadge(): ?string
    {
        return (string) ClientConfiguration::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client Info')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Client Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('api_key')
                            ->label('API Key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('secret_key')
                            ->label('Secret Key')
                            ->required()
                            ->password()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Technical Settings')
                    ->schema([
                        Forms\Components\TextInput::make('tps')
                            ->label('Max TPS')
                            ->numeric()
                            ->default(5)
                            ->minValue(1),
                        Forms\Components\KeyValue::make('service_routing')
                            ->label('Service Routing')
                            ->addButtonLabel('Add Route')
                            ->keyLabel('Service Type')
                            ->valueLabel('Vendor')
                            ->reactive()
                            ->nullable(),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('api_key')
                    ->label('API Key')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('tps')
                    ->label('TPS')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),
            ])

            ->filters([
                //
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListClientConfigurations::route('/'),
            'create' => Pages\CreateClientConfiguration::route('/create'),
            'edit' => Pages\EditClientConfiguration::route('/{record}/edit'),
        ];
    }
}

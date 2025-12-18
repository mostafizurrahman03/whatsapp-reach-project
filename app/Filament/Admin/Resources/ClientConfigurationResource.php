<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ClientConfigurationResource\Pages;
use App\Models\ClientConfiguration;
use App\Models\Service;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


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

                Forms\Components\Section::make('Client Credentials')
                    ->schema([

                        Forms\Components\Select::make('user_id')
                            ->label('Linked User (Optional)')
                            ->relationship('user', 'name')
                            ->preload() 
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('client_api_key')
                            ->label('Client API Key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('client_secret_key')
                            ->label('Client Secret Key')
                            ->required()
                            ->password()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sender_ids')
                            ->label('Sender IDs (comma separated)')
                            ->helperText('Example: 8809610980262, WEHUB, BRANDX')
                            ->afterStateHydrated(function ($component, $state) {
                                $component->state(is_array($state) ? implode(',', $state) : $state);
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return array_map('trim', explode(',', $state));
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Billing & Limits')
                    ->schema([

                        Forms\Components\TextInput::make('balance')
                            ->label('Balance')
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('rate_per_sms')
                            ->label('Rate Per SMS')
                            ->numeric()
                            ->default(0.00),

                        Forms\Components\TextInput::make('tps')
                            ->label('TPS Limit')
                            ->numeric()
                            ->default(5)
                            ->minValue(1),

                    ])
                    ->columns(3),

                Forms\Components\Section::make('Routing & Security')
                    ->schema([

                        Forms\Components\KeyValue::make('service_routing')
                            ->label('Service Routing (service => vendor)')
                            ->keyLabel('Service (sms / whatsapp / voice)')
                            ->valueLabel('Vendor Name')
                            ->addButtonLabel('Add Route')
                            ->nullable(),

                        Forms\Components\KeyValue::make('allowed_ips')
                            ->label('Allowed IPs')
                            ->keyLabel('Index')
                            ->valueLabel('IP Address')
                            ->addButtonLabel('Add IP')
                            ->nullable(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                    ])
                    ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('client_api_key')
                    ->label('API Key')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sender_ids')
                    ->label('Sender IDs')
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->searchable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tps')
                    ->label('TPS')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),
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
        return [];
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

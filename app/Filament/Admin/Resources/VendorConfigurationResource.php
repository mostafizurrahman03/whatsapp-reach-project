<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VendorConfigurationResource\Pages;
use App\Models\VendorConfiguration;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VendorConfigurationResource extends Resource
{
    protected static ?string $model = VendorConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Messaging Gateway';
    protected static ?string $navigationLabel = 'Vendor Configurations';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_id')
                    ->label('Service')
                    ->relationship('service', 'name')
                    ->required(),

                Forms\Components\TextInput::make('vendor_name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('base_url')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('send_sms_url')
                    ->label('Send SMS Endpoint')
                    ->placeholder('/v3/send-sms')
                    ->maxLength(255),

                Forms\Components\TextInput::make('api_key')->maxLength(255),
                Forms\Components\TextInput::make('secret_key')->maxLength(255),

                Forms\Components\TextInput::make('tps')
                    ->numeric()
                    ->default(10)
                    ->required(),

                //  Add Sender IDs (JSON)
                // Forms\Components\Textarea::make('sender_ids')
                //     ->label('Sender IDs (JSON Array)')
                //     ->placeholder('["8809610980262", "WEHUB1"]')
                //     ->columnSpanFull(),

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


                Forms\Components\Textarea::make('extra_config')
                    ->label("Extra Config (JSON)")
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('ip_whitelist')
                    ->label("IP Whitelist (JSON Array)")
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('vendor_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('base_url')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tps')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendorConfigurations::route('/'),
            'create' => Pages\CreateVendorConfiguration::route('/create'),
            'edit'  => Pages\EditVendorConfiguration::route('/{record}/edit'),
        ];
    }
}


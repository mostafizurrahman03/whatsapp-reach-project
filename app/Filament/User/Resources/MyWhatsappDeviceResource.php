<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyWhatsappDeviceResource\Pages;
use App\Filament\User\Resources\MyWhatsappDeviceResource\RelationManagers;
use App\Models\MyWhatsappDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;

class MyWhatsappDeviceResource extends Resource
{ 
    protected static ?string $model = MyWhatsappDevice::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationGroup = 'WhatsApp';
    protected static ?string $pluralModelLabel = 'WhatsApp Devices';
    protected static ?string $navigationLabel = 'Devices';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('user_id')
                //     ->relationship('user', 'name')
                //     ->searchable()
                //     ->nullable()
                //     ->label('User'),

                Forms\Components\TextInput::make('device_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Device ID'),

                // Forms\Components\TextInput::make('device_name')
                //     ->maxLength(100)
                //     ->label('Device Name'),

                // Forms\Components\TextInput::make('phone_number')
                //     ->tel()
                //     ->maxLength(20)
                //     ->label('Phone Number'),

                // Forms\Components\Textarea::make('qr_code')
                //     ->rows(2)
                //     ->label('QR Code (Raw)'),

                // Forms\Components\FileUpload::make('qr_image')
                //     ->image()
                //     ->directory('whatsapp/qr')
                //     ->maxSize(1024 * 2) // 2MB
                //     ->label('QR Image'),

                // Forms\Components\Textarea::make('session_data')
                //     ->rows(5)
                //     ->label('Session Data'),

                // Forms\Components\Select::make('status')
                //     ->options([
                //         'pending' => 'Pending',
                //         'connected' => 'Connected',
                //         'disconnected' => 'Disconnected',
                //     ])
                //     ->default('pending'),

                // Forms\Components\Toggle::make('connected')
                //     ->label('Connected')
                //     ->default(false),

                // Forms\Components\DateTimePicker::make('last_connected_at')
                //     ->label('Last Connected'),

                // Forms\Components\DateTimePicker::make('last_disconnected_at')
                //     ->label('Last Disconnected'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                // Tables\Columns\TextColumn::make('user.name')->label('User')->sortable(),
                Tables\Columns\TextColumn::make('device_id')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('device_name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone_number')->sortable()->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'connected',
                        'danger' => 'disconnected',
                    ])
                    ->sortable(),

                Tables\Columns\IconColumn::make('connected')
                    ->boolean()
                    ->label('Connected'),

                // Tables\Columns\ImageColumn::make('qr_image')
                //     ->square()
                //     ->height(40)
                //     ->label('QR'),

                Tables\Columns\TextColumn::make('last_connected_at')->dateTime()->toggleable(),
                Tables\Columns\TextColumn::make('last_disconnected_at')->dateTime()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'connected' => 'Connected',
                        'disconnected' => 'Disconnected',
                    ]),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Modal-based QR Code Action
                Action::make('qrCode')
                    ->label('Show QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('WhatsApp Device QR Code')
                    ->modalSubheading('Scan this QR code with your WhatsApp app.')
                    ->modalContent(function ($record) {
                        $qrCodeImage = null;
                        try {
                            $response = Http::get(config('services.whatsapp.api_base_url') . "/api/device/{$record->id}/qr");
                            // $response = Http::get('http://127.0.0.1:3333/api/device/' . $record->id . '/qr');
                            print_r($response);
                            if ($response->successful()) {
                                $data = $response->json();
                                $qrCodeImage = $data['qr'] ?? null;
                            }
                        } catch (\Exception $e) {
                            $qrCodeImage = null;
                        }

                        return view('filament.resources.mydevice-resource.pages.qr-code-modal-content', [
                            'record' => $record,
                            'qrCodeImage' => $qrCodeImage,
                        ]);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyWhatsappDevices::route('/'),
            'create' => Pages\CreateMyWhatsappDevice::route('/create'),
            'edit' => Pages\EditMyWhatsappDevice::route('/{record}/edit'),
            // 'view' => Pages\ViewMyWhatsappDevice::route('/{record}'),
        ];
    }
}


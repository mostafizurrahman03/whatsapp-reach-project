<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyWhatsappDeviceResource\Pages;
use App\Models\MyWhatsappDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
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
                Forms\Components\TextInput::make('device_id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Device ID'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('device_id')->sortable()->searchable(),
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
                            
                            $response = Http::get("http://43.231.78.204:3333/api/device/Md__Sakib/qr");

                            if ($response->successful()) {
                                $data = $response->json();
                                $qrCodeImage = $data['qr'] ?? null;
                            }
                        } catch (\Exception $e) {
                            $qrCodeImage = null;
                        }

                        return view('filament.my-whatsapp-device.qr-code-modal', [
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
        ];
    }
}

<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyWhatsappDeviceResource\Pages;
use App\Models\MyWhatsappDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class MyWhatsappDeviceResource extends Resource
{
    protected static ?string $model = MyWhatsappDevice::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationGroup = 'WhatsApp';
    protected static ?string $pluralModelLabel = 'WhatsApp Devices';
    protected static ?string $navigationLabel = 'Devices';
    protected static ?int $navigationSort = 1;


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
                Tables\Columns\TextColumn::make('phone_number')
                    ->sortable()
                    ->searchable()
                    ->label('WhatsApp Number'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'connected',
                        'danger'  => 'disconnected',
                    ])
                    ->sortable()
                    ->label('Connection Status'),
                // Tables\Columns\IconColumn::make('connected')
                //     ->boolean()
                //     ->label('Connected'),
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
                // Tables\Actions\EditAction::make(),

                // QR Code modal
                Action::make('qrCode')
                    ->label('Show QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->modalHeading('WhatsApp Device QR Code')
                    ->modalSubheading('Scan this QR code with your WhatsApp app.')
                    ->modalContent(function ($record) {

                        $qrCodeImage = null;
                        $connected = false;

                        try {
                            // GET status API
                            $statusResponse = Http::get("http://43.231.78.204:3333/api/device/{$record->device_id}/status");
                            if ($statusResponse->successful()) {
                                $statusData = $statusResponse->json();
                                $connected = $statusData['connected'] ?? false;

                                // Only fetch QR if not connected
                                if (!$connected) {
                                    $qrResponse = Http::get("http://43.231.78.204:3333/api/device/{$record->device_id}/qr");
                                    if ($qrResponse->successful()) {
                                        $qrCodeImage = $qrResponse->json()['qr'] ?? null;
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            $qrCodeImage = null;
                        }

                        return view('filament.my-whatsapp-device.qr-code-modal', [
                            'record'      => $record,
                            'qrCodeImage' => $qrCodeImage,
                            'connected'   => $connected,
                        ]);
                    }),


                // Refresh Status action

                Action::make('refreshStatus')
                    ->label('Refresh Status')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function ($record) {
                        try {
                            $response = Http::get("http://43.231.78.204:3333/api/device/{$record->device_id}/status");
                            
                            if ($response->successful()) {
                                $data = $response->json();
                                $record->status = $data['connected'] ? 'connected' : 'disconnected';
                                $record->phone_number = $data['number'] ?? null;
                                $record->save();

                                // Add a success notification
                                Notification::make()
                                    ->title('Status refreshed successfully!')
                                    ->success()
                                    ->send();
                            } else {
                                // Add a failure notification for unsuccessful API response
                                Notification::make()
                                    ->title('Failed to refresh status.')
                                    ->body('The API request was not successful.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            // Add a failure notification for connection errors
                            Notification::make()
                                ->title('Connection Error')
                                ->body('Could not connect to the API. Please try again later.')
                                ->danger()
                                ->send();
                        }
                    }),
                //Reconnect whatsapp device
                    Action::make('reconnectDevice')
                    ->label('Reconnect Status')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function ($record) {
                        try {
                            $response = Http::post("http://43.231.78.204:3333/api/device/{$record->device_id}/reconnect");

                            if ($response->successful()) {
                                $data = $response->json();

                                // Map reconnecting → connected
                                $record->status = ($data['status'] === 'reconnecting')
                                    ? 'connected'
                                    : 'disconnected';

                                $record->phone_number = $data['number'] ?? null;
                                $record->save();

                                Notification::make()
                                    ->title('Device reconnected successfully!')
                                    ->success()
                                    ->send();
                            } else {
                                \Log::error('Reconnect failed: '.$response->status().' '.$response->body());

                                Notification::make()
                                    ->title('Failed to reconnect device.')
                                    ->body('The API request was not successful. ('.$response->status().')')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            \Log::error('Reconnect Error: '.$e->getMessage());

                            Notification::make()
                                ->title('Connection Error')
                                ->body('Could not connect to the API. Please try again later.')
                                ->danger()
                                ->send();
                        }
                    }),


                // Delete action
                
                Action::make('logoutDevice')
                    ->label('Logout Device')
                    ->icon('heroicon-o-power')
                    ->action(function ($record) {
                        try {
                            Http::post("http://43.231.78.204:3333/api/device/{$record->device_id}/logout");
                        } catch (\Exception $e) {
                            // ignore
                        }
                    })
                    ->requiresConfirmation() 
                    ->color('warning'),

                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        try {
                            Http::delete("http://43.231.78.204:3333/api/device/{$record->device_id}");
                        } catch (\Exception $e) {
                            // ignore
                        }
                    }),

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
     public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}

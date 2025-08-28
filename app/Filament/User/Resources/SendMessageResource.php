<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\SendMessageResource\Pages;
use App\Filament\User\Resources\SendMessageResource\RelationManagers;
use App\Models\SendMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\RichEditor;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SendMessageResource extends Resource
{
    protected static ?string $model = SendMessage::class;
    protected static ?string $navigationGroup = 'Send Single Message';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Send Message';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('device_id')
                    ->label('Select Device')
                    ->options(function () {
                        return Auth::user()
                            ->myWhatsappDevices()
                            ->get()
                            ->mapWithKeys(function ($device) {
                                // fallback to device_id if device_name is null
                                $label = $device->device_name ?? $device->device_id;
                                return [$device->device_id => $label];
                            })
                            ->toArray();
                    })
                    ->required()
                    ->searchable(),
                

                

                // Forms\Components\Select::make('deviceId')
                //     ->label('Select Device')
                //     ->options(function () {
                //         try {
                //             $response = Http::get('http://43.231.78.204:3333/api/devices');

                //             if ($response->successful()) {
                //                 return collect($response->json())
                //                     ->mapWithKeys(function ($device) {
                //                         // Label: pushname (number)
                //                         $pushname = $device['pushname'] ?? $device['deviceId'];
                //                         $number   = $device['number'] ? '+'.$device['number'] : $device['deviceId'];

                //                         $label = "{$pushname} ({$number})";

                //                         return [$device['deviceId'] => $label];
                //                     })
                //                     ->toArray();
                //             }

                //             return [];
                //         } catch (\Exception $e) {
                //             return [];
                //         }
                //     })
                //     ->required()
                //     ->searchable(),



                Forms\Components\TextInput::make('number')
                    ->label('Receiver Number')
                    ->required(),

                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->rows(3)
                    ->required(),

                
                // RichEditor::make('message')
                //     ->toolbarButtons([
                //         'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link',
                //         'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd',
                //         'blockquote', 'codeBlock', 'bulletList', 'orderedList',
                //         'table',
                //         'undo', 'redo',
                //     ]),

                Forms\Components\Toggle::make('is_sent')
                    ->label('Sent')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device.phone_number')
                ->label('Sender Number')
                ->formatStateUsing(fn ($state, $record) => $state ?? $record->device_id)
                ->sortable()
                ->searchable(),     
                Tables\Columns\TextColumn::make('number')->label('Receiver'),
                Tables\Columns\TextColumn::make('message')->label('Message')->limit(50),
                Tables\Columns\IconColumn::make('is_sent')->boolean()->label('Sent'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created At'),
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
    // public static function getNavigationUrl(): string
    // {
    //     return static::getUrl('create'); // default index page, but we define create page
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSendMessages::route('/'),
            'create' => Pages\CreateSendMessage::route('/create'),
            'edit' => Pages\EditSendMessage::route('/{record}/edit'),
        ];
    }
}

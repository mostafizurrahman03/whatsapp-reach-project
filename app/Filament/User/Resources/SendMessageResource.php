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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Grid;
use App\Models\MessageTemplate;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;


class SendMessageResource extends Resource
{
    protected static ?string $model = SendMessage::class;
    protected static ?string $navigationGroup = 'Whatsapp Single Message';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Send Message';
    protected static ?int $navigationSort = 2;


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
                    ->placeholder('8801XXXXXXXXX')
                    ->helperText('Only valid phone number is allowed.')
                    ->required(),

                Forms\Components\Select::make('template_id')
                    ->label('📄 Choose Template')
                    ->options(MessageTemplate::pluck('name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $template = MessageTemplate::find($state);

                        if ($template) {
                            $set('message', $template->content);
                        }
                    })
                    ->helperText('Select a template to auto-fill the message.'),

                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->rows(3)
                    ->required()
                    ->reactive() // makes it live-update on typing
                    ->helperText(fn ($get, $state) => strlen($state) . ' / 500 characters used')
                    ->maxLength(500),

                
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

                Tables\Columns\TextColumn::make('number')
                    ->label('Receiver')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\IconColumn::make('is_sent')
                    ->label('Sent')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->toggleable(), // <-- Toggleable
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->filters([
                // Optional: Filter if needed
            ])
            ->headerActions([
            FilamentExportHeaderAction::make('export')
                ->label('Export Data')
                ->fileName('bulk_send_message_recipients')
                ->defaultFormat('xlsx')
                ->withHiddenColumns() // keeps hidden columns hidden
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->actionsColumnLabel('Action')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export-selected')
                    ->label('Export Selected')
                    ->fileName('selected_recipients_export')
                    ->defaultFormat('xlsx'),
                ]),
            ]);
    }

    // public static function getNavigationUrl(): string
    // {
    //     return static::getUrl('create'); // default index page, but we define create page
    // }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Message Details')
                    ->description('Here are the details of your sent message.')
                    ->schema([
                        Grid::make(2)->schema([
                       
                            TextEntry::make('device.phone_number')
                                ->label('Sender Number')
                                ->placeholder('N/A')
                                ->badge()
                                ->color('success'),

                            TextEntry::make('number')
                                ->label('Receiver Number')
                                ->placeholder('N/A')
                                ->badge()
                                ->color('primary'),

                            // Message
                            TextEntry::make('message')
                                ->label('Message Content')
                                ->columnSpanFull()
                                ->markdown()
                                ->copyable()
                                ->copyMessage('Message copied!')
                                ->color('gray'),

                            // Sent status 
                            IconEntry::make('is_sent')
                                ->label('Sent Status')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            // Created & Updated
                            // TextEntry::make('created_at')
                            //     ->label('Created At')
                            //     ->dateTime('d M, Y h:i A')
                            //     ->color('warning'),

                            // TextEntry::make('updated_at')
                            //     ->label('Last Updated')
                            //     ->dateTime('d M, Y h:i A')
                            //     ->color('info'),
                        ]),
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-chat-bubble-left-right'),
                // Timestamps Section
                Section::make('Timestamps')
                    ->description('Created and last updated times.')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('created_at')
                                ->label('Created At')
                                ->dateTime('d M Y, h:i A')
                                ->badge()
                                ->color('success'),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime('d M Y, h:i A')
                                ->badge()
                                ->color('warning'),
                        ]),
                    ])
                    ->columns(2)
                    ->collapsed(),
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
            'index' => Pages\ListSendMessages::route('/'),
            'create' => Pages\CreateSendMessage::route('/create'),
            'edit' => Pages\EditSendMessage::route('/{record}/edit'),
        ];
    }
        // Filter messages by logged-in user
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}

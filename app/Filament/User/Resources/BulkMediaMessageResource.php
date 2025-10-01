<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BulkMediaMessageResource\Pages;
use App\Filament\User\Resources\BulkMediaMessageResource\RelationManagers\RecipientsRelationManager;
use App\Models\BulkMediaMessage;
use Filament\Forms;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Group;

class BulkMediaMessageResource extends Resource
{
    protected static ?string $model = BulkMediaMessage::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2; // Menu serial
    protected static ?string $navigationGroup = 'Whatsapp Bulk Message';
    protected static ?string $navigationLabel = 'Send Bulk Media Message';

    public static function form(Form $form): Form
    {
        return $form
    ->schema([
        Forms\Components\Grid::make(2) // 2-column grid
            ->schema([
                // Left column (1st column)
                Forms\Components\Section::make('Message Details')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Select::make('device_id')
                            ->label('Select Device')
                            ->options(function () {
                                return Auth::user()
                                    ->myWhatsappDevices()
                                    ->get()
                                    ->mapWithKeys(fn($device) => [
                                        $device->device_id => $device->device_name ?? $device->device_id
                                    ])->toArray();
                            })
                            ->required()
                            ->searchable(),

                        Forms\Components\Textarea::make('message')
                            ->label('Message')
                            ->rows(4)
                            ->required(),

                        Forms\Components\FileUpload::make('media_url')
                            ->label('Attachment')
                            ->disk('public')
                            ->directory('messages')
                            ->downloadable()
                            ->openable()
                            ->previewable(true)
                            ->acceptedFileTypes([
                                'image/jpeg','image/png','application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'image/*',
                            ]),

                        Forms\Components\Textarea::make('caption')
                            ->label('Caption')
                            ->rows(2)
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_sent')
                            ->label('Sent')
                            ->default(false),
                    ]),

                // Right column (2nd column)
                Forms\Components\Section::make('Recipients')
                    ->columnSpan(1)
                    ->schema([
                        // Forms\Components\Repeater::make('recipients')
                        //     // ->relationship('recipients')
                        //     ->schema([
                        //         Forms\Components\TextInput::make('number')
                        //             ->label('Receiver Number')
                        //             ->placeholder('8801XXXXXXXXX')
                        //             ->required(),
                        //     ])
                        //     ->createItemButtonLabel('➕ Add another number')
                        //     ->columns(1),

                        TagsInput::make('recipients')
                            ->label('Receiver Numbers')
                            ->placeholder('8801XXXXXXXXX')
                            ->required()
                            ->separator(','), // Use comma as separator


                        Forms\Components\FileUpload::make('recipients_csv')
                            ->label('Upload CSV of Numbers')
                            ->helperText('Upload a CSV file containing phone numbers in one column.')
                            ->disk('public')
                            ->directory('recipients')
                            ->acceptedFileTypes(['text/csv', 'text/plain'])
                            ->maxSize(2048),
                    ]),
            ]),
    ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device.phone_number')
                    ->label('Sender Number')
                    ->sortable()
                    ->searchable(),

                // Tables\Columns\TextColumn::make('number')->label('Receiver')->sortable(),
                Tables\Columns\TextColumn::make('receivers')
                    ->label('Receivers')
                    ->getStateUsing(fn ($record) => $record->recipients->pluck('number')->implode(', ')),

                Tables\Columns\TextColumn::make('message')
                ->label('Message')
                ->limit(25)
                ->searchable()
                ->sortable(), 

                Tables\Columns\ImageColumn::make('media_url')
                    ->label('Attachment')
                    ->disk('public')
                    ->height(50)
                    ->width(50)
                    ->extraAttributes(['class' => 'rounded'])
                    ->url(fn ($record) => $record->media_url ? asset('storage/' . $record->media_url) : null)
                    // ->visible(...)  // temporarily remove
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('caption')->label('Caption')->limit(25)->searchable(),
                Tables\Columns\IconColumn::make('is_sent')->boolean()->label('Sent'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created At')    ->sortable(), 

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

    

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Message & Recipients Section in two columns
                Section::make('Message & Recipients')
                    ->description('Details of the message, attachment, and recipients.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Grid::make(2)->schema([
                            // Left column: Message + Recipients
                            Group::make([
                                TextEntry::make('device.phone_number')
                                    ->label('Sender Number')
                                    ->placeholder('N/A')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('message')
                                    ->label('Message Content')
                                    ->markdown()
                                    ->copyable()
                                    ->copyMessage('Message copied!')
                                    ->color('gray'),

                                ImageEntry::make('media_url')
                                    ->label('Attachment')
                                    ->disk('public')
                                    ->height(120)
                                    ->width(120)
                                    ->circular(false)
                                    ->url(fn($record) => $record->media_url ? asset('storage/' . $record->media_url) : null)
                                    ->openUrlInNewTab(),
                                TextEntry::make('caption')
                                    ->label('Caption')
                                    ->placeholder('N/A')
                                    ->color('primary'),    

                                
                            ])->columnSpan(1),

                            // Right column: Attachment + Caption + Sent Status
                            Group::make([
                                // ImageEntry::make('media_url')
                                //     ->label('Attachment')
                                //     ->disk('public')
                                //     ->height(120)
                                //     ->width(120)
                                //     ->circular(false)
                                //     ->url(fn($record) => $record->media_url ? asset('storage/' . $record->media_url) : null)
                                //     ->openUrlInNewTab(),
                                TextEntry::make('recipients')
                                    ->label('Recipients')
                                    ->getStateUsing(fn($record) => $record->recipients->pluck('number')->implode(', '))
                                    ->copyable()
                                    ->copyMessage('Recipients copied!')
                                    ->color('primary'),

                                IconEntry::make('is_sent')
                                    ->label('Sent Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ])->columnSpan(1),
                        ]),
                    ])
                    ->columns(2)
                    ->collapsible(),
                // Right Column: Recipients
                    Section::make('Recipients')
                        ->description('All recipients of this bulk message.')
                        ->icon('heroicon-o-users')
                        ->schema([
                            TextEntry::make('recipients')
                                ->label('Recipients')
                                ->getStateUsing(fn($record) => $record->recipients->pluck('number')->implode(', '))
                                ->columnSpanFull()
                                ->copyable()
                                ->copyMessage('Recipients copied!')
                                ->color('gray'),
                        ])
                        ->columns(1)
                        ->collapsed(),
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
            RecipientsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulkMediaMessages::route('/'),
            'create' => Pages\CreateBulkMediaMessage::route('/create'),
            'edit' => Pages\EditBulkMediaMessage::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
    
}

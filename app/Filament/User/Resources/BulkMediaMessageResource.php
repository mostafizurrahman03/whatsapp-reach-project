<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BulkMediaMessageResource\Pages;
use App\Filament\User\Resources\BulkMediaMessageResource\RelationManagers\RecipientsRelationManager;
use App\Models\BulkMediaMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BulkMediaMessageResource extends Resource
{
    protected static ?string $model = BulkMediaMessage::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2; // Menu serial
    protected static ?string $navigationGroup = 'Messaging';

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
                        Forms\Components\Repeater::make('recipients')
                            ->relationship('recipients')
                            ->schema([
                                Forms\Components\TextInput::make('number')
                                    ->label('Receiver Number')
                                    ->placeholder('8801XXXXXXXXX')
                                    ->required(),
                            ])
                            ->createItemButtonLabel('➕ Add another number')
                            ->columns(1),

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

                Tables\Columns\TextColumn::make('message')->label('Message')->limit(25)->sortable(), 

                Tables\Columns\ImageColumn::make('media_url')
                    ->label('Attachment')
                    ->disk('public')
                    ->height(50)
                    ->width(50)
                    ->extraAttributes(['class' => 'rounded'])
                    ->url(fn ($record) => $record->media_url ? asset('storage/' . $record->media_url) : null)
                    // ->visible(...)  // temporarily remove
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('caption')->label('Caption')->limit(25),
                Tables\Columns\IconColumn::make('is_sent')->boolean()->label('Sent'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created At')    ->sortable(), 

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

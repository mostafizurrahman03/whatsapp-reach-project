<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\SendMediaMessageResource\Pages;
use App\Filament\User\Resources\SendMediaMessageResource\RelationManagers;
use App\Models\SendMediaMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SendMediaMessageResource extends Resource
{
    protected static ?string $model = SendMediaMessage::class;
    protected static ?string $navigationGroup = 'Send Single Message';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Send Media Message';
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

                Forms\Components\TextInput::make('number')
                    ->label('Receiver Number')
                    ->placeholder('8801XXXXXXXXX')
                    ->required(),

                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->rows(4)
                    ->required(),

                // RichEditor::make('message')
                //     ->toolbarButtons([
                //         'bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link',
                //         'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd',
                //         'blockquote', 'codeBlock', 'bulletList', 'orderedList',
                //         'table', 'attachFiles',
                //         'undo', 'redo',
                //     ]),
                Forms\Components\FileUpload::make('media_url')
                    ->label('Attachment')
                    ->disk('public') // Which disk the files will be saved to
                    ->directory('messages') // Folder where files will be stored
                    // ->multiple() // Support for multiple file uploads
                    ->downloadable() // Allow files to be downloaded
                    ->openable() // Allow files to be opened
                    ->helperText('You can upload one file (jpg, jpeg, png, pdf, docx, xlsx, csv, mp4). Max size: 2MB each.')
                    ->previewable(true) // Set true if you want image preview
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/*',
                    ]),
                Forms\Components\Textarea::make('caption')
                    ->label('Caption')
                    ->placeholder('Enter caption for the attachment')
                    ->rows(2)
                    ->maxLength(255),        
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
                Tables\Columns\TextColumn::make('number')->label('Receiver')->sortable(),
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
            'index' => Pages\ListSendMediaMessages::route('/'),
            'create' => Pages\CreateSendMediaMessage::route('/create'),
            'edit' => Pages\EditSendMediaMessage::route('/{record}/edit'),
        ];
    }
      // Filter media messages by logged-in user
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}

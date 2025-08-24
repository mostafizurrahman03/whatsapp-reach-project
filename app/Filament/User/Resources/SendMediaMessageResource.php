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
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SendMediaMessageResource extends Resource
{
    protected static ?string $model = SendMediaMessage::class;
    protected static ?string $navigationGroup = 'Send Single Message';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    Forms\Components\FileUpload::make('attachments')
                        ->disk('public') // Which disk the files will be saved to
                        ->directory('messages') // Folder where files will be stored
                        ->multiple() // Support for multiple file uploads
                        ->downloadable() // Allow files to be downloaded
                        ->openable() // Allow files to be opened
                        ->previewable(false) // Set true if you want image preview
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'image/*',
                        ]),


                Forms\Components\Toggle::make('is_sent')
                    ->label('Sent')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
            'index' => Pages\ListSendMediaMessages::route('/'),
            'create' => Pages\CreateSendMediaMessage::route('/create'),
            'edit' => Pages\EditSendMediaMessage::route('/{record}/edit'),
        ];
    }
}

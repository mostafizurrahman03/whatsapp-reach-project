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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid;
use App\Models\MessageTemplate;


class SendMediaMessageResource extends Resource
{
    protected static ?string $model = SendMediaMessage::class;
    protected static ?string $navigationGroup = 'Whatsapp Single Message';

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
                    Forms\Components\Select::make('template_id')
                    ->label('📄 Choose Template')
                    ->options(MessageTemplate::pluck('name', 'id'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $template = MessageTemplate::find($state);
                        if ($template) {
                            $set('message', $template->content);
                            $set('caption', $template->caption ?? '');

                            // Fix for single file
                            if ($template->media_url) {
                                $set('media_url', [$template->media_url]); //  wrap in array
                            } else {
                                $set('media_url', null);
                            }
                        }
                    })
                    ->helperText('Select a message template to auto-fill message, caption, and attachment.'),
                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->rows(4)
                    ->required()
                    ->reactive() // makes it live-update on typing
                    ->helperText(fn ($get, $state) => strlen($state) . ' / 500 characters used')
                    ->maxLength(500),

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
                    ->downloadable() 
                    ->openable() 
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
                Tables\Columns\TextColumn::make('number')->label('Receiver')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('message')->label('Message')->limit(25)->searchable()->sortable(), 

                Tables\Columns\ImageColumn::make('media_url')
                    ->label('Attachment')
                    ->disk('public')
                    ->height(50)
                    ->width(50)
                    ->extraAttributes(['class' => 'rounded'])
                    ->url(fn ($record) => $record->media_url ? asset('storage/' . $record->media_url) : null)
                    // ->visible(...)  // temporarily remove
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('caption')->label('Caption')->searchable()->limit(25),
                Tables\Columns\IconColumn::make('is_sent')->boolean()->label('Sent'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created At')->toggleable()->sortable(), 
                // Tables\Columns\TextColumn::make('action')
                //     ->label('Action')
                //     ->formatStateUsing(fn ($record) => 'View | Edit | Delete')
                //     ->html() 
                //     // ->extraAttributes(['class' => 'text-center']) 
                //     ->alignCenter() 
                //     ->sortable(false)
                //     ->searchable(false),


            ])
            ->filters([
                //
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
                Section::make('Media Message Details')
                    ->description('Detailed information about this media message.')
                    ->schema([
                        Grid::make(2)->schema([

                            // Sender Number
                            TextEntry::make('device.phone_number')
                                ->label('Sender Number')
                                ->placeholder('N/A')
                                ->badge()
                                ->color('success'),

                            // Receiver Number
                            TextEntry::make('number')
                                ->label('Receiver Number')
                                ->placeholder('N/A')
                                ->badge()
                                ->color('primary'),

                            // Message
                            TextEntry::make('message')
                                ->label('Message')
                                ->markdown()
                                ->copyable()
                                ->copyMessage('Message copied!')
                                ->columnSpanFull(),

                            // Media (Image/File Preview)
                            ImageEntry::make('media_url')
                                ->label('Attachment')
                                ->disk('public')
                                ->height(200)
                                ->hidden(fn ($record) => empty($record->media_url))
                                ->columnSpanFull(),

                            // Caption
                            TextEntry::make('caption')
                                ->label('Caption')
                                ->placeholder('No caption')
                                ->color('gray')
                                ->columnSpanFull(),

                            // Sent Status
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
                    ->icon('heroicon-o-photo')
                    ->collapsible(),
                
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

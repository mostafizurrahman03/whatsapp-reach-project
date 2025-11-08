<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BulkMediaMessageResource\Pages;
use App\Filament\User\Resources\BulkMediaMessageResource\RelationManagers\RecipientsRelationManager;
use App\Models\BulkMediaMessage;
use App\Models\MessageTemplate;
use App\Models\Lead;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\Select;
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
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\FileUpload;
// use Filament\Forms\Components\Html;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Filament\Notifications\Notification;



class BulkMediaMessageResource extends Resource
{
    protected static ?string $model = BulkMediaMessage::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3; // Menu serial
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
                                // Select Device
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

                                // Template Selector + Auto-fill Fields
                                Forms\Components\Group::make()
                                    ->schema([
                                        // Forms\Components\Select::make('template_id')
                                        //     ->label('📄 Choose Template')
                                        //     ->options(MessageTemplate::pluck('name', 'id'))
                                        //     ->searchable()
                                        //     ->reactive()
                                        //     ->afterStateUpdated(function ($state, callable $set) {
                                        //         $template = MessageTemplate::find($state);
                                        //         if ($template) {
                                        //             $set('message', $template->content);
                                        //             $set('caption', $template->caption ?? '');
                                        //             $set('media_url', $template->media_url ?? null);
                                        //         }
                                        //     })
                                        //     ->helperText('Select a message template to auto-fill message, caption, and attachment.'),


                                Forms\Components\Select::make('campaign_id')
                                    ->label('Select Campaign')
                                    ->options(fn () => Campaign::where('user_id', auth()->id())->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->visible(fn () => Campaign::where('user_id', auth()->id())->exists()),       
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
                                            ->reactive()
                                            ->helperText(fn($get, $state) => strlen($state) . ' / 500 characters used')
                                            ->maxLength(500),

                                        Forms\Components\FileUpload::make('media_url')
                                            ->label('Attachment')
                                            ->disk('public')
                                            ->directory('messages')
                                            ->downloadable()
                                            ->openable()
                                            ->helperText('You can upload one file (jpg, jpeg, png, pdf, docx, xlsx, csv, mp4). Max size: 2MB each.')
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
                                            ->reactive()
                                            ->maxLength(255),    
                                    ]),

                                // Sent Toggle
                                Forms\Components\Toggle::make('is_sent')
                                    ->label('Sent')
                                    ->default(false),
                            ]),

                        // Right column (2nd column)
                        // Forms\Components\Section::make('Recipients')
                        //     ->columnSpan(1)
                        //     ->schema([
                        //         TagsInput::make('recipients')
                        //             ->label('Receiver Numbers')
                        //             ->placeholder('8801XXXXXXXXX')
                        //             ->required()
                        //             ->separator(','),

                        //         Forms\Components\FileUpload::make('recipients_csv')
                        //             ->label('Upload CSV of Numbers')
                        //             ->helperText('Upload a CSV file containing phone numbers in one column.')
                        //             ->disk('public')
                        //             ->directory('recipients')
                        //             ->acceptedFileTypes(['text/csv', 'text/plain'])
                        //             ->maxSize(2048),
                        // ]),

                        // Right column (2nd column)
                        FormSection::make('Recipients')
                            ->columnSpan(1)
                            ->schema([
                                Radio::make('input_method')
                                    ->label('Select Input Method')
                                    ->options([
                                        'manual' => 'Manual Entry',
                                        'csv' => 'Upload CSV File',
                                        'lead' => 'From Lead List',
                                    ])
                                    ->default('manual')
                                    ->inline()
                                    ->reactive(),

                                
                                //  Show only the logged-in user’s leads
                                Select::make('lead_id')
                                    ->label('Select Lead Name')
                                    ->options(function () {
                                        // leads fetch for logged-in user
                                        $leads = Lead::where('user_id', auth()->id())
                                            ->select('name', 'id') 
                                            ->get()
                                            ->groupBy('name');  

                                        $options = [];
                                        foreach ($leads as $name => $group) {
                                            $count = $group->count(); // row count according to name
                                            // if name null, then shows phone number
                                            $displayName = $name ?? $group->first()->phone;
                                            // dropdown option
                                            $options[$group->first()->id] = "{$displayName} ({$count})";
                                        }

                                        return $options;
                                    })
                                    ->searchable()
                                    ->placeholder('Select a lead')
                                    ->visible(fn ($get) => $get('input_method') === 'lead'),


        
                                // TagsInput::make('recipients')
                                //     ->label('Receiver Numbers')
                                //     ->placeholder('8801XXXXXXXXX')
                                //     ->required()
                                //     ->separator(',')
                                //     ->visible(fn ($get) => $get('input_method') === 'manual')
                                //     // ->helperText('Select a message template to auto-fill message, caption, and attachment.')
                                //     ->hint('Enter multiple numbers separated by commas. Only phone numbers are allowed.')
                                //     ->hintIcon('heroicon-o-information-circle')
                                //     ->hintColor('success'), // icon color

                                // TagsInput::make('recipients')
                                //     ->label('Receiver Numbers')
                                //     ->placeholder('8801XXXXXXXXX')
                                //     ->required()
                                //     ->reactive()
                                //     ->separator(',')
                                //     ->visible(fn ($get) => $get('input_method') === 'manual')
                                //     ->helperText('Enter multiple numbers separated by commas. Only valid phone numbers are allowed.')
                                //     // ->hint('Enter multiple numbers separated by commas. Only valid phone numbers are allowed.')
                                //     // ->hintIcon('heroicon-o-information-circle')
                                //     // ->hintColor('success')
                                //     ->rules([
                                //         'required',
                                //         'array',
                                //         function ($attribute, $values, $fail) {
                                //             foreach ($values as $value) {
                                //                 // Remove spaces and symbols
                                //                 $number = preg_replace('/\D/', '', $value);

                                //                 // Validate Bangladeshi phone number (13 digits with 88 prefix)
                                //                 if (!preg_match('/^88(01[3-9]\d{8})$/', $number)) {
                                //                     $fail("Each number in {$attribute} must be a valid Bangladeshi phone number.");
                                //                 }
                                //             }
                                //         },
                                //     ]),

                                TagsInput::make('recipients')
                                    ->label('Receiver Numbers')
                                    ->placeholder('8801XXXXXXXXX')
                                    ->required()
                                    ->reactive()
                                    ->separator(',')
                                    ->visible(fn ($get) => $get('input_method') === 'manual')
                                    ->helperText('Enter multiple numbers separated by commas. Only valid Bangladeshi phone numbers are allowed.')
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (is_array($state)) {
                                            foreach ($state as $value) {
                                                // Remove all non-digit characters
                                                $number = preg_replace('/\D/', '', $value);

                                                // Validate Bangladeshi number format (+8801XXXXXXXXX or 8801XXXXXXXXX or 01XXXXXXXXX)
                                                if (!preg_match('/^(?:\+?88)?01[3-9]\d{8}$/', $number)) {
                                                    Notification::make()
                                                        ->title('Invalid Phone Number')
                                                        ->body("{$value} is not a valid Bangladeshi number. Format: 8801XXXXXXXXX")
                                                        ->danger()
                                                        ->send();

                                                    // Optionally, remove invalid numbers
                                                    $set('recipients', array_filter($state, fn ($num) => $num !== $value));

                                                    break;
                                                }
                                            }
                                        }
                                    }),

                                    
                                
                                FileUpload::make('recipients_csv')
                                    ->label('Upload CSV of Numbers')
                                    ->helperText('Upload a CSV file containing phone numbers in one column.')
                                    ->disk('public')
                                    ->directory('recipients')
                                    ->acceptedFileTypes(['text/csv', 'text/plain'])
                                    ->maxSize(2048)
                                    ->visible(fn ($get) => $get('input_method') === 'csv'),


                                // Sample CSV download link
                                ViewField::make('sample_csv_link')
                                    ->view('filament.user.pages.sample-csv-link')
                                    ->visible(fn ($get) => $get('input_method') === 'csv'),    
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
                    ->limit(15)
                    ->getStateUsing(fn ($record) => $record->recipients->pluck('number')->implode(', ')),
                Tables\Columns\TextColumn::make('campaign.name')
                    ->label('Campaign')
                    ->limit(20)
                    ->searchable()
                    ->default('N/A')
                    ->sortable(), 
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(15)
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

                Tables\Columns\TextColumn::make('caption')->label('Caption')->limit(15)->searchable(),
                Tables\Columns\IconColumn::make('is_sent')->boolean()->label('Sent'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Created At')->toggleable()->sortable(), 

            ])
            ->defaultSort('created_at', direction: 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
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

                                TextEntry::make('campaign.name')
                                    ->label('Campaign')
                                    ->default('N/A')
                                    ->placeholder('N/A')
                                    ->limit(30)
                                    ->badge() 
                                    ->color('primary'), 

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

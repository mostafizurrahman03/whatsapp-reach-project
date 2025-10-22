<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BulkSendMessageResource\Pages;
use App\Models\BulkSendMessage;
use App\Models\MyWhatsappDevice;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
// use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Group;
use App\Models\MessageTemplate;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Radio;
// use Filament\Forms\Components\Html;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;



class BulkSendMessageResource extends Resource
{
    protected static ?string $model = BulkSendMessage::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Whatsapp Bulk Message';
    protected static ?string $navigationLabel = 'Send Bulk Message';
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),

                Forms\Components\Grid::make(2)
                    ->schema([
                        // Left Column
                        Forms\Components\Section::make('Message Details')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('device_id')
                                    ->label('Select Device')
                                    ->options(fn () => MyWhatsappDevice::query()
                                        ->when(auth()->check(), fn ($q) => $q->where('user_id', auth()->id()))
                                        ->orderByRaw('COALESCE(device_name, device_id) asc')
                                        ->get()
                                        ->mapWithKeys(fn ($d) => [
                                            $d->device_id => ($d->device_name ?: $d->device_id),
                                        ])
                                        ->toArray())
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                // Template Selector + Auto-fill
                                Select::make('template_id')
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

                                Textarea::make('message')
                                    ->label('Message')
                                    ->rows(6)
                                    ->required()
                                    ->reactive() // makes it live-update on typing
                                    ->helperText(fn ($get, $state) => strlen($state) . ' / 500 characters used')
                                    ->maxLength(500),

                                Toggle::make('is_sent')
                                    ->label('Marked as sent?')
                                    ->default(false)
                                    ->helperText('System will update this automatically when sending.'),
                            ]),

                        // Right Column
                        // Forms\Components\Section::make('Recipients')
                        //     ->columnSpan(1)
                        //     ->schema([
                        //         TagsInput::make('recipients_list')
                        //             ->label('Receiver Numbers')
                        //             ->placeholder('8801XXXXXXXXX')
                        //             ->separator(',')
                        //             ->required()
                        //             ->default(fn ($record) => $record?->recipients->pluck('number')->toArray() ?? [])
                        //             ->afterStateHydrated(function ($component, $state, $record) {
                        //                 if ($record) {
                        //                     $component->state($record->recipients->pluck('number')->toArray());
                        //                 }
                        //             })
                        //             ->dehydrateStateUsing(fn ($state) => $state) // keep state during save
                        //             ->saveRelationshipsUsing(function ($record, $state) {
                        //                 // $record->recipients()->delete();
                        //                 // foreach ($state as $number) {
                        //                 //     $record->recipients()->create(['number' => $number]);
                        //                 // }
                        //             }),

                        //         FileUpload::make('recipients_csv')
                        //             ->label('Upload CSV of Numbers')
                        //             ->helperText('Upload a CSV file containing phone numbers in one column.')
                        //             ->disk('public')
                        //             ->directory('recipients')
                        //             ->acceptedFileTypes(['text/csv', 'text/plain'])
                        //             ->maxSize(2048),
                        //     ]),
                        // Right column (2nd column)
                        FormSection::make('Recipients')
                            ->columnSpan(1)
                            ->schema([

                                // Radio button to select input method
                                Radio::make('input_method')
                                    ->label('Select Input Method')
                                    ->options([
                                        'manual' => 'Manual Entry',
                                        'csv' => 'Upload CSV File',
                                        'lead' => 'From Lead List',

                                    ])
                                    ->default('manual')
                                    ->inline()    // horizontal layout
                                    ->reactive(), // required for live visibility update


                                //  Show only the logged-in user’s leads
                                Select::make('lead_id')
                                    ->label('Select Lead Name')
                                    ->options(
                                        fn () => Lead::query()
                                            ->where('user_id', auth()->id())
                                            ->selectRaw("id, COALESCE(name, phone) as display_name") // fallback if name is null
                                            ->pluck('display_name', 'id')
                                    )
                                    ->searchable()
                                    ->placeholder('Select a lead')
                                    ->visible(fn ($get) => $get('input_method') === 'lead'),    

                                // Manual input (TagsInput)
                                // TagsInput::make('recipients_list')
                                //     ->label('Receiver Numbers')
                                //     ->placeholder('8801XXXXXXXXX')
                                //     ->separator(',')
                                //     ->required()
                                //     ->helperText('Enter multiple numbers separated by commas. Only valid phone numbers are allowed.')
                                //     ->default(fn ($record) => $record?->recipients->pluck('number')->toArray() ?? [])
                                //     ->afterStateHydrated(function ($component, $state, $record) {
                                //         if ($record) {
                                //             $component->state($record->recipients->pluck('number')->toArray());
                                //         }
                                //     })
                                //     ->dehydrateStateUsing(fn ($state) => $state)
                                //     ->saveRelationshipsUsing(function ($record, $state) {
                                //         // $record->recipients()->delete();
                                //         // foreach ($state as $number) {
                                //         //     $record->recipients()->create(['number' => $number]);
                                //         // }
                                //     })
                                //     ->visible(fn ($get) => $get('input_method') === 'manual'),

                                

                                TagsInput::make('recipients_list')
                                    ->label('Receiver Numbers')
                                    ->placeholder('8801XXXXXXXXX')
                                    ->separator(',')
                                    ->required()
                                    ->helperText('Enter multiple numbers separated by commas. Only valid Bangladeshi phone numbers are allowed.')
                                    ->default(fn ($record) => $record?->recipients->pluck('number')->toArray() ?? [])
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->recipients->pluck('number')->toArray());
                                        }
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (is_array($state)) {
                                            $validNumbers = [];
                                            foreach ($state as $value) {
                                                $number = preg_replace('/\D/', '', $value); // Remove non-digits

                                                //  Validate Bangladeshi number (+8801XXXXXXXXX / 8801XXXXXXXXX / 01XXXXXXXXX)
                                                if (preg_match('/^(?:\+?88)?01[3-9]\d{8}$/', $number)) {
                                                    $validNumbers[] = $value;
                                                } else {
                                                    Notification::make()
                                                        ->title('Invalid Phone Number')
                                                        ->body("{$value} is not a valid Bangladeshi number.\nFormat: 8801XXXXXXXXX or 01XXXXXXXXX")
                                                        ->danger()
                                                        ->send();
                                                }
                                            }

                                            //  Remove invalid numbers automatically
                                            $set('recipients_list', $validNumbers);
                                        }
                                    })
                                    ->dehydrateStateUsing(fn ($state) => $state)
                                    ->saveRelationshipsUsing(function ($record, $state) {
                                        // Optional: handle save manually
                                        // $record->recipients()->delete();
                                        // foreach ($state as $number) {
                                        //     $record->recipients()->create(['number' => $number]);
                                        // }
                                    })
                                    ->visible(fn ($get) => $get('input_method') === 'manual'),


                                // CSV upload
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
                TextColumn::make('device.phone_number')
                    ->label('Sender Number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('recipients')
                    ->label('Receivers')
                    ->limit(25)
                    ->getStateUsing(fn ($record) => $record->recipients->pluck('number')->implode(', ')),

                TextColumn::make('message')
                    ->label('Message')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_sent')
                    ->label('Sent')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d M Y, h:i A')
                    ->label('Created At')
                    ->toggleable()
                    ->sortable(),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->filters([
                TernaryFilter::make('is_sent')
                    ->label('Sent status')
                    ->placeholder('All')
                    ->trueLabel('Sent')
                    ->falseLabel('Not sent')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_sent', true),
                        false: fn (Builder $query) => $query->where('is_sent', false),
                        blank: fn (Builder $query) => $query,
                    ),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
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
                Section::make('Message & Recipients')
                    ->description('Details of the message and recipients.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Grid::make(2)->schema([
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
                            ])->columnSpan(1),

                            Group::make([
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulkSendMessages::route('/'),
            'create' => Pages\CreateBulkSendMessage::route('/create'),
            'edit' => Pages\EditBulkSendMessage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(auth()->check(), fn (Builder $q) => $q->where('user_id', auth()->id()));
    }
}

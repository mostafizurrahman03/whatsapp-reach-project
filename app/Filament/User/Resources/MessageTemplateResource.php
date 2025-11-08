<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MessageTemplateResource\Pages;
use App\Models\MessageTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid;

class MessageTemplateResource extends Resource
{
    protected static ?string $model = MessageTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Message Templates';
    protected static ?string $pluralLabel = 'Message Templates';
    protected static ?string $modelLabel = 'Message Template';
    protected static ?string $navigationGroup = 'Campaign Management';
    protected static ?int $navigationSort = 2;

    // Form: Create / Edit
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        // Left Column
                        Forms\Components\Section::make('Basic Info')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->relationship('user', 'name')
                                    ->label('User')
                                    ->required()
                                    ->visible(fn() => auth()->user()->role === 'admin'),

                                Forms\Components\Hidden::make('user_id')
                                    ->default(fn() => auth()->id())
                                    ->visible(fn() => auth()->user()->role !== 'admin'),

                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(150),

                                Forms\Components\Textarea::make('content')
                                    ->required()
                                    ->rows(5)
                                    ->label('Message'),

                                Forms\Components\Select::make('type')
                                    ->label('Template Type')
                                    ->options([
                                        'whatsapp' => 'WhatsApp',
                                        'sms' => 'SMS',
                                        'email' => 'Email',
                                    ])
                                    ->default('whatsapp')
                                    ->required(),
                            ]),

                        // Right Column
                        Forms\Components\Section::make('WhatsApp Options')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Textarea::make('caption')
                                    ->label('Caption')
                                    ->rows(3)
                                    ->maxLength(255)
                                    ->visible(fn($get) => $get('type') === 'whatsapp'),

                                Forms\Components\FileUpload::make('media_url')
                                    ->label('Attachment')
                                    ->disk('public')
                                    ->directory('templates')
                                    ->previewable(true)
                                    ->downloadable()
                                    ->visible(fn($get) => $get('type') === 'whatsapp'),
                            ]),
                    ]),
            ]);
    }

    // Table: List view
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->visible(fn() => auth()->user()->role === 'admin'),

                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('type')->badge(),

                Tables\Columns\TextColumn::make('caption')
                    ->label('Caption')
                    ->formatStateUsing(fn($state, $record) => 
                        $record->type === 'whatsapp' ? $state : '-'
                    )
                    ->wrap()
                    ->placeholder('N/A')
                    ->extraAttributes(['class' => 'whitespace-normal']),


                // Tables\Columns\TextColumn::make('caption')
                //     ->label('Caption')
                //     ->limit(50),
                    // ->wrap()
                    // ->visible(fn($record) => $record?->type === 'whatsapp'),

                Tables\Columns\ImageColumn::make('media_url')
                    ->label('Attachment')
                    ->disk('public')
                    ->height(50)
                    ->width(50)
                    ->getStateUsing(fn($record) => 
                        $record->type === 'whatsapp' ? $record->media_url : null
                    )
                    ->placeholder('N/A'),
                

                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'sms' => 'SMS',
                        'email' => 'Email',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->actionsColumnLabel('Action');
    }

    public static function getRelations(): array
    {
        return [];
    }

    // Non-admin only sees their own templates
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if(auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        return $query;
    }

    protected static function booted(): void
    {
        static::creating(function (MessageTemplate $template) {
            if(!$template->user_id && auth()->check()) {
                $template->user_id = auth()->id();
            }
        });
    }

    // Infolist: View page
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Template Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextEntry::make('name')
                                ->label('Template Name'),

                            TextEntry::make('type')
                                ->label('Template Type'),

                            TextEntry::make('content')
                                ->label('Message')
                                ->columnSpanFull()
                                ->markdown(),

                            TextEntry::make('caption')
                                ->label('Caption')
                                ->columnSpanFull()
                                ->visible(fn($record) => $record?->type === 'whatsapp'),

                            ImageEntry::make('media_url')
                                ->label('Attachment')
                                ->disk('public')
                                ->height(150)
                                ->width(150)
                                ->hidden(fn($record) => empty($record?->media_url))
                                ->columnSpanFull(),

                            TextEntry::make('created_at')
                                ->label('Created At')
                                ->dateTime('d M Y, h:i A'),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->dateTime('d M Y, h:i A'),
                        ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessageTemplates::route('/'),
            'create' => Pages\CreateMessageTemplate::route('/create'),
            'edit' => Pages\EditMessageTemplate::route('/{record}/edit'),
        ];
    }
}

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

class MessageTemplateResource extends Resource
{
    protected static ?string $model = MessageTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Message Templates';
    protected static ?string $pluralLabel = 'Message Templates';
    protected static ?string $modelLabel = 'Message Template';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 5;

    // Form: Create / Edit
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Admin: select any user
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User')
                    ->required()
                    ->visible(fn() => auth()->user()->role === 'admin'),

                // Non-admin: hidden field, auto-fill
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => auth()->id())
                    ->visible(fn() => auth()->user()->role !== 'admin'),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(150),

                Forms\Components\Textarea::make('content')
                    ->required()
                    ->rows(5),

                Forms\Components\Select::make('type')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'sms' => 'SMS',
                        'email' => 'Email',
                    ])
                    ->default('whatsapp')
                    ->required(),
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
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'sms' => 'SMS',
                        'email' => 'Email',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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

    // Auto-fill user_id if missing (extra safeguard)
    protected static function booted(): void
    {
        static::creating(function (MessageTemplate $template) {
            if(!$template->user_id && auth()->check()) {
                $template->user_id = auth()->id();
            }
        });
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

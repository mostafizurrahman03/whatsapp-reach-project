<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\SmsBulkMessageResource\Pages;
use App\Models\SmsBulkMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SmsBulkMessageResource extends Resource
{
    protected static ?string $model = SmsBulkMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'SMS Bulk Messages';
    protected static ?string $navigationGroup = 'Messaging';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->preload() 
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('service')
                    ->options([
                        'sms' => 'SMS',
                        'whatsapp' => 'WhatsApp',
                        'voice' => 'Voice Call',
                        'ivr' => 'IVR',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('content')
                    ->label('Message Content')
                    ->rows(4)
                    ->required(),

                Forms\Components\Repeater::make('recipients')
                    ->label('Recipients List')
                    ->schema([
                        Forms\Components\TextInput::make('number')
                            ->label('Recipient Number')
                            ->required(),
                    ])
                    ->columns(1)
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                    ])
                    ->default('pending')
                    ->required(),

                Forms\Components\KeyValue::make('response')
                    ->label('API Response')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('service')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'sent',
                        'danger' => 'failed',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSmsBulkMessages::route('/'),
            'create' => Pages\CreateSmsBulkMessage::route('/create'),
            'edit' => Pages\EditSmsBulkMessage::route('/{record}/edit'),
        ];
    }
}

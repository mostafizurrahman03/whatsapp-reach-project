<?php

namespace App\Filament\User\Resources\BulkMediaMessageResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;

class RecipientsRelationManager extends RelationManager
{
    protected static string $relationship = 'recipients';
    protected static ?string $recordTitleAttribute = 'number';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('number')
                ->label('Phone Number')
                ->required()
                ->maxLength(255),

            Forms\Components\Toggle::make('is_sent')
                ->label('Sent Status')
                ->default(false),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label('Phone Number'),
                Tables\Columns\IconColumn::make('is_sent')->label('Sent Status')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}


<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BulkSendMessageResource\Pages;
use App\Filament\User\Resources\BulkSendMessageResource\RelationManagers;
use App\Models\BulkSendMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BulkSendMessageResource extends Resource
{
    protected static ?string $model = BulkSendMessage::class;
    protected static ?string $navigationGroup = 'Send Bulk Message';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Send Bulk Message';
    protected static ?int $navigationSort = 3;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulkSendMessages::route('/'),
            'create' => Pages\CreateBulkSendMessage::route('/create'),
            'edit' => Pages\EditBulkSendMessage::route('/{record}/edit'),
        ];
    }
}

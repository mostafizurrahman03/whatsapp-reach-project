<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ClientMessageResource\Pages;
use App\Filament\Admin\Resources\ClientMessageResource\RelationManagers;
use App\Models\ClientMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ClientMessageResource extends Resource
{
    protected static ?string $model = ClientMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Website Settings';
    protected static ?string $navigationLabel = 'Client Message';
    public static function getNavigationBadge(): ?string
    {
        return (string) \App\Models\ClientMessage::count();
    }
    // public static function getNavigationBadgeColor(): ?string
    // {
    //     return (string) \App\Models\ClientMessage::count() > 0 ? 'danger' : 'gray';
    // }

 

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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->searchable()
                    ->tooltip(fn ($record) => $record->message)
                    ->limit(20),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\Filter::make('received_date')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('From'),
                        Forms\Components\DatePicker::make('created_until')->label('To'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    })
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
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

                Section::make('Client Details')
                    ->description('Basic information submitted by the client')
                    ->icon('heroicon-o-identification')
                    ->collapsible()
                    ->columns(2)
                    ->schema([

                        TextEntry::make('name')
                            ->label('Client Name')
                            ->icon('heroicon-o-user')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->badge()
                            ->color('info'),

                        TextEntry::make('subject')
                            ->label('Subject')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('created_at')
                            ->label('Received At')
                            ->dateTime('d M Y h:i A')
                            ->icon('heroicon-o-clock')
                            ->badge()
                            ->color('success'),
                    ])
                    ->columnSpanFull(),

                Section::make('Message')
                    ->description('Full message sent by the client')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([

                        TextEntry::make('message')
                            ->label('Message Body')
                            ->columnSpanFull()
                            ->markdown() 
                            ->prose(),   
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
            'index' => Pages\ListClientMessages::route('/'),
            'create' => Pages\CreateClientMessage::route('/create'),
            'edit' => Pages\EditClientMessage::route('/{record}/edit'),
        ];
    }
}

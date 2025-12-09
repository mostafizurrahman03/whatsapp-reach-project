<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ApiLogResource\Pages;
use App\Models\ApiLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\JsonEntry;

class ApiLogResource extends Resource
{
    protected static ?string $model = ApiLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'System Logs';
    protected static ?string $navigationLabel = 'API Logs';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]); 
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('service')->label('Service')->searchable(),
                Tables\Columns\TextColumn::make('vendor')->label('Vendor')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => fn ($state) => $state === 'SUCCESS',
                        'danger' => fn ($state) => $state === 'FAILED',
                        'warning' => fn ($state) => $state === 'PENDING',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('d M Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make('Basic Info')
                    ->schema([
                        TextEntry::make('client'),
                        TextEntry::make('service'),
                        TextEntry::make('vendor'),
                        TextEntry::make('status')
                            ->badge()
                            ->colors([
                                'success' => fn ($state) => $state === 'SUCCESS',
                                'danger' => fn ($state) => $state === 'FAILED',
                                'warning' => fn ($state) => $state === 'PENDING',
                            ]),
                        TextEntry::make('created_at')->dateTime('d M Y h:i A'),
                    ])
                    ->columns(2),

                Section::make('Request Payload')
                    ->schema([
                        JsonEntry::make('request_payload')
                            ->label('Request JSON')
                            ->copyable(),
                    ]),

                Section::make('Response Payload')
                    ->schema([
                        JsonEntry::make('response_payload')
                            ->label('Response JSON')
                            ->copyable(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiLogs::route('/'),
            'view' => Pages\ViewApiLog::route('/{record}'),
        ];
    }
}

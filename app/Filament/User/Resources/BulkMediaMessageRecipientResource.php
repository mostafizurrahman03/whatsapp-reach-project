<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BulkMediaMessageRecipientResource\Pages;
use App\Models\BulkMediaMessageRecipient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;

class BulkMediaMessageRecipientResource extends Resource
{
    protected static ?string $model = BulkMediaMessageRecipient::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationGroup = 'Whatsapp Bulk Message';
    protected static ?string $pluralLabel = 'Bulk Media Message Recipients';
    protected static ?int $navigationSort = 3; // Menu serial
    protected static ?string $label = 'Recipient';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bulk_media_message_id')
                    ->label('Message')
                    ->relationship('bulkMediaMessage', 'message')
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('number')
                    ->label('Recipient Number')
                    ->placeholder('e.g. 017XXXXXXXX')
                    ->required()
                    ->maxLength(20),

                Forms\Components\Toggle::make('is_sent')
                    ->label('Sent')
                    ->default(false),

                Forms\Components\DateTimePicker::make('sent_at')
                    ->label('Sent At')
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Sender Device
                TextColumn::make('bulkMediaMessage.device.device_id')
                    ->label('Sender Device')
                    ->sortable()
                    ->searchable(),

                // Message Body
                TextColumn::make('bulkMediaMessage.message')
                    ->label('Message')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->bulkMediaMessage?->message),

                // Recipient
                TextColumn::make('number')
                    ->label('Recipient')
                    ->sortable()
                    ->searchable(),

                // Sent status
                IconColumn::make('is_sent')
                    ->label('Sent')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),

                // Sent time
                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                 //  Sent Status Filter
    Tables\Filters\TernaryFilter::make('is_sent')
    ->label('Sent Status')
    ->placeholder('All')
    ->trueLabel('Sent')
    ->falseLabel('Not Sent')
    ->queries(
        true: fn (Builder $query) => $query->where('is_sent', true),
        false: fn (Builder $query) => $query->where('is_sent', false),
        blank: fn (Builder $query) => $query,
    ),

//  Date Range Filter
Tables\Filters\Filter::make('created_at')
    ->label('Created Date')
    ->form([
        Forms\Components\DatePicker::make('from')->label('From'),
        Forms\Components\DatePicker::make('until')->label('Until'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
    }),

//  Device Filter
Tables\Filters\SelectFilter::make('bulkMediaMessage.device_id')
    ->label('Sender Device')
    ->relationship('bulkMediaMessage.device', 'device_id')
    ->searchable()
    ->preload(),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulkMediaMessageRecipients::route('/'),
            'create' => Pages\CreateBulkMediaMessageRecipient::route('/create'),
            'edit' => Pages\EditBulkMediaMessageRecipient::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BulkSendMessageRecipientResource\Pages;
use App\Models\BulkSendMessageRecipient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
// ✅ এই দুইটা line সবচেয়ে important (Export package থেকে)
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class BulkSendMessageRecipientResource extends Resource
{
    protected static ?string $model = BulkSendMessageRecipient::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationGroup = 'Whatsapp Bulk Message';
    protected static ?string $pluralLabel = 'Bulk Send Message Recipients';
    protected static ?int $navigationSort = 4;
    protected static ?string $label = 'Recipient';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bulk_send_message_id')
                    ->label('Bulk Message')
                    ->relationship('bulkSendMessage', 'title')
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
                // ID
                // TextColumn::make('id')->sortable(),

                // Sender Device
                TextColumn::make('sender')
                    ->label('Sender')
                    ->state(function ($record) {
                        $device = $record->bulkSendMessage?->device;

                        if (!$device) {
                            return '—';
                        }

                        $name = $device->device_id ?: 'Unknown Device';
                        $phone = $device->phone_number;

                        return $phone ? "{$name} ({$phone})" : $name;
                    })
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search) {
                        return $query->whereHas('bulkSendMessage.device', function (Builder $q) use ($search) {
                            $q->where('device_name', 'like', "%{$search}%")
                              ->orWhere('phone_number', 'like', "%{$search}%")
                              ->orWhere('device_id', 'like', "%{$search}%");
                        });
                    }),

                // Message Title/Body
                TextColumn::make('bulkSendMessage.message')
                    ->label('Message')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->bulkSendMessage?->message),

                // Recipient Number
                TextColumn::make('number')
                    ->label('Recipient')
                    ->sortable()
                    ->searchable(),

                // Sent Status
                IconColumn::make('is_sent')
                    ->label('Sent')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),

                // Sent Time
                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),

                // Created At
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Sent Status Filter
                TernaryFilter::make('is_sent')
                    ->label('Sent Status')
                    ->placeholder('All')
                    ->trueLabel('Sent')
                    ->falseLabel('Not Sent')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_sent', true),
                        false: fn (Builder $query) => $query->where('is_sent', false),
                        blank: fn (Builder $query) => $query,
                    ),

                // Created Date Filter
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

                // Device Filter
                Tables\Filters\SelectFilter::make('bulkSendMessage.device_id')
                    ->label('Sender Device')
                    ->relationship('bulkSendMessage.device', 'device_id')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
            FilamentExportHeaderAction::make('export')
                ->label('Export Data')
                ->fileName('bulk_send_message_recipients')
                ->defaultFormat('xlsx')
                ->withHiddenColumns() // keeps hidden columns hidden
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export-selected')
                    ->label('Export Selected')
                    ->fileName('selected_recipients_export')
                    ->defaultFormat('xlsx'),
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
            'index' => Pages\ListBulkSendMessageRecipients::route('/'),
            'create' => Pages\CreateBulkSendMessageRecipient::route('/create'),
            'edit' => Pages\EditBulkSendMessageRecipient::route('/{record}/edit'),
        ];
    }
}

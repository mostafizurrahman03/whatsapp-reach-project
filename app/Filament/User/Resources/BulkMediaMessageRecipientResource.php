<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BulkMediaMessageRecipientResource\Pages;
use App\Models\BulkMediaMessageRecipient;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class BulkMediaMessageRecipientResource extends Resource
{
    protected static ?string $model = BulkMediaMessageRecipient::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $pluralLabel = 'Bulk Media Message Recipients';
    protected static ?int $navigationSort = 5; // Menu serial
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
                TextColumn::make('sender')
                    ->label('Sender')
                    ->state(function ($record) {
                        $device = $record->bulkMediaMessage?->device;

                        if (!$device) {
                            return '—';
                        }

                        $name = $device->device_id ?: 'Unknown Device'; 
                        $phone = $device->phone_number;

                        return $phone ? "{$name} ({$phone})" : $name;
                    })
                    ->sortable()
                    ->searchable(query: function (Builder $query, string $search) {
                        return $query->whereHas('bulkMediaMessage.device', function (Builder $q) use ($search) {
                            $q->where(function ($query) use ($search) {
                                $query->where('device_name', 'like', "%{$search}%")
                                    ->orWhere('phone_number', 'like', "%{$search}%")
                                    ->orWhere('device_id', 'like', "%{$search}%");
                            });
                        });
                    }),

                // Campaign
                TextColumn::make('bulkMediaMessage.campaign.name')
                    ->label('campaign')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->bulkMediaMessage?->campaign?->name),

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
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->filters([
                 //  Device Filter
                Tables\Filters\SelectFilter::make('bulkMediaMessage.device_id')
                    ->label('Sender Device')
                    ->relationship('bulkMediaMessage.device', 'device_id')
                    ->placeholder('All')
                    ->searchable()
                    ->preload(),
                 
                
                // Campaign Filter
                // Tables\Filters\SelectFilter::make('bulkMediaMessage.campaign_id')
                //     ->label('Campaign')
                //     ->relationship('bulkMediaMessage.campaign', 'name')
                //     ->placeholder('All')
                //     ->searchable()
                //     ->preload(),

                  Tables\Filters\SelectFilter::make('bulk_media_message_id')
                    ->label('Campaign')
                    ->options(Campaign::pluck('name', 'id'))
                    ->placeholder('All')
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            // Filter by the selected campaign
                            $query->whereHas('bulkMediaMessage.campaign', function ($q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }
                    })
                    ->preload(),


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
                    })->columnSpan(2)->columns(2)

               
                ],layout: FiltersLayout::AboveContent)
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
                            // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBulkMediaMessageRecipients::route('/'),
            'create' => Pages\CreateBulkMediaMessageRecipient::route('/create'),
            'edit' => Pages\EditBulkMediaMessageRecipient::route('/{record}/edit'),
        ];
    }
}

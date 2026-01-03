<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\SmsBulkMessageResource\Pages;
use App\Models\SmsBulkMessage;
use App\Models\VendorConfiguration;
use Illuminate\Database\Eloquent\Builder;
// use App\Models\SmsSenderId;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\Action;
use App\Exports\SmsBulkMessagesExport;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;


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
                    ->default(auth()->id())
                    ->relationship('user', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('vendor_configuration_id')
                    ->label('Vendor')
                    ->relationship('vendorConfiguration', 'vendor_name')
                    ->preload()
                    ->searchable()
                    ->nullable(),

                Forms\Components\Select::make('service_id')
                    ->label('Service')
                    ->relationship('service', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),

                Select::make('sender_id')
                    ->label('Sender ID')
                    ->options(function () {
                        return VendorConfiguration::where('is_active', true)
                            ->pluck('sender_ids')   // assume array/json column
                            ->flatten()
                            ->filter()
                            ->unique()
                            ->mapWithKeys(fn ($sender) => [
                                $sender => $sender, // value => label
                            ])
                            ->toArray();
                    })
                    ->searchable()
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

                Forms\Components\TextInput::make('total_recipients')
                    ->label('Total Recipients')
                    ->numeric()
                    ->default(0)
                    ->disabled(),

                Forms\Components\TextInput::make('success_count')
                    ->label('Success Count')
                    ->numeric()
                    ->default(0)
                    ->disabled(),

                Forms\Components\TextInput::make('failed_count')
                    ->label('Failed Count')
                    ->numeric()
                    ->default(0)
                    ->disabled(),

                Forms\Components\TextInput::make('cost')
                    ->label('Cost')
                    ->numeric()
                    ->default(0.00)
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'sent' => 'Sent',
                        'partial' => 'Partial',
                        'failed' => 'Failed',
                    ])
                    ->default('pending')
                    ->required(),

                Forms\Components\KeyValue::make('response')
                    ->label('API Response')
                    ->nullable(),

                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->label('Scheduled At')
                    ->nullable(),

                Forms\Components\DateTimePicker::make('sent_at')
                    ->label('Sent At')
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

                Tables\Columns\TextColumn::make('vendorConfiguration.vendor_name')
                    ->label('Vendor')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sender_id')
                    ->label('Sender ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'sent',
                        'secondary' => 'partial',
                        'danger' => 'failed',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_recipients')
                    ->label('Total'),

                Tables\Columns\TextColumn::make('success_count')
                    ->label('Success'),

                Tables\Columns\TextColumn::make('failed_count')
                    ->label('Failed'),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Cost')
                    ->money('BDT', true),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')

            ->filters([
                // Sender Device Filter
                Tables\Filters\SelectFilter::make('sender_id')
                    ->label('Sender Device')
                    ->options(
                        SmsBulkMessage::query()
                            ->whereNotNull('sender_id')
                            ->distinct()
                            ->pluck('sender_id', 'sender_id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload(),

                // Sent Status Filter
                Tables\Filters\TernaryFilter::make('is_sent')
                    ->label('Sent Status')
                    ->trueLabel('Sent')
                    ->falseLabel('Not Sent')
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_sent', true),
                        false: fn (Builder $query) => $query->where('is_sent', false),
                        blank: fn (Builder $query) => $query,
                    ),

                //  Tables\Filters\SelectFilter::make('status')
                //         ->options([
                //             'pending' => 'Pending',
                //             'processing' => 'Processing',
                //             'sent' => 'Sent',
                //             'partial' => 'Partial',
                //             'failed' => 'Failed',
                //         ]),    

                // Created Date Range Filter
                Tables\Filters\Filter::make('created_at')
                    ->label('Created Date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From'),
                        Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn ($q, $date) => $q->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn ($q, $date) => $q->whereDate('created_at', '<=', $date)
                            );
                    })
                    ->columnSpan(2)->columns(2),
            ], layout: FiltersLayout::AboveContent)

            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('Export Data')
                    ->fileName('bulk_send_message_recipients')
                    ->defaultFormat('xlsx')
                    ->withHiddenColumns()
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),

                FilamentExportBulkAction::make('export-selected')
                    ->label('Export Selected')
                    ->fileName('selected_recipients_export')
                    ->defaultFormat('xlsx'),
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


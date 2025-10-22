<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\Builder;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Campaigns';
    protected static ?string $pluralLabel = 'Campaigns';
    protected static ?string $modelLabel = 'Campaign';
    protected static ?string $navigationGroup = 'Campaign Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Campaign Details') // Section wrapping all fields
                    ->schema([
                        // Admin only: assign user
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('User')
                            ->required()
                            ->visible(fn () => auth()->user()->role === 'admin'),

                        TextInput::make('name')
                            ->label('Campaign Name')
                            ->required()
                            ->maxLength(150),

                        // Template field (commented out for now)
                        // Select::make('template_id')
                        //     ->relationship('template', 'name')
                        //     ->required()
                        //     ->label('Template'),

                        Select::make('channel')
                            ->label('Channel')
                            ->options([
                                'whatsapp' => 'WhatsApp',
                                'sms' => 'SMS',
                                'email' => 'Email',
                            ])
                            ->default('whatsapp')
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'running' => 'Running',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                            ])
                            ->default('draft')
                            ->required(),

                        DateTimePicker::make('scheduled_at')
                            ->label('Scheduled At'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->visible(fn () => auth()->user()->role === 'admin'),

                Tables\Columns\TextColumn::make('name')->searchable(),
                // Tables\Columns\TextColumn::make('template.name')->label('Template'),
                Tables\Columns\TextColumn::make('channel')->badge(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('scheduled_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'sms' => 'SMS',
                        'email' => 'Email',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'running' => 'Running',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // Multi-user query: non-admin sees only own campaigns
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        return $query;
    }

    protected static function booted(): void
    {
        static::creating(function (Campaign $campaign) {
            if (auth()->check() && auth()->user()->role !== 'admin') {
                $campaign->user_id = auth()->id();
            }
        });
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
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}

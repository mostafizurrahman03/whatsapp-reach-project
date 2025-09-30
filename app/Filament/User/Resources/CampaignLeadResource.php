<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\CampaignLeadResource\Pages;
use App\Models\CampaignLead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\Builder;

class CampaignLeadResource extends Resource
{
    protected static ?string $model = CampaignLead::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'Campaign Leads';
    protected static ?string $pluralLabel = 'Campaign Leads';
    protected static ?string $modelLabel = 'Campaign Lead';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
    ->schema([
        Select::make('campaign_id')
            ->relationship(
                name: 'campaign',
                titleAttribute: 'name',
                modifyQueryUsing: fn ($query) =>
                    auth()->user()->role !== 'admin'
                        ? $query->where('user_id', auth()->id())
                        : $query
            )
            ->label('Campaign')
            ->required(),

        Select::make('lead_id')
            ->relationship('lead', 'phone')
            ->label('Lead Phone')
            ->required(),

        Select::make('status')
            ->options([
                'pending' => 'Pending',
                'sent' => 'Sent',
                'failed' => 'Failed',
                'delivered' => 'Delivered',
                'read' => 'Read',
            ])
            ->default('pending')
            ->required(),

        DateTimePicker::make('sent_at')->label('Sent At'),
    ]);

    }
    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campaign.name')->label('Campaign')->sortable(),
                TextColumn::make('lead.phone')->label('Lead Phone')->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('sent_at')->dateTime()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                        'delivered' => 'Delivered',
                        'read' => 'Read',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // Multi-user query: non-admin sees only leads of own campaigns
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->user()->role !== 'admin') {
            $query->whereHas('campaign', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }
        return $query;
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
            'index' => Pages\ListCampaignLeads::route('/'),
            'create' => Pages\CreateCampaignLead::route('/create'),
            'edit' => Pages\EditCampaignLead::route('/{record}/edit'),
        ];
    }
}

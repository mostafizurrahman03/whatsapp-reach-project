<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\CampaignLeadResource\Pages;
use App\Models\CampaignLead;
use App\Models\Lead;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CampaignLeadResource extends Resource
{
    protected static ?string $model = CampaignLead::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'Campaign Leads';
    protected static ?string $pluralLabel = 'Campaign Leads';
    protected static ?string $modelLabel = 'Campaign Lead';
    // protected static ?string $navigationGroup = 'Campaign Management';
    // protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
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
            ])
            ->headerActions([
                Tables\Actions\Action::make('upload_csv')
                    ->label('Upload Leads CSV/Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        Select::make('campaign_id')
                            ->label('Select Campaign')
                            ->options(
                                Campaign::query()
                                    ->when(auth()->user()->role !== 'admin', fn($q) => $q->where('user_id', auth()->id()))
                                    ->pluck('name', 'id')
                            )
                            ->required(),

                        FileUpload::make('file')
                            ->label('Upload File (.csv or .xlsx)')
                            ->required()
                            ->acceptedFileTypes([
                                'text/csv',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel'
                            ])
                            ->disk('public')
                            ->directory('uploads/campaign-leads'),
                    ])
                    ->action(function (array $data) {
                        $path = Storage::disk('public')->path($data['file']);
                        $extension = pathinfo($path, PATHINFO_EXTENSION);

                        $rows = [];

                        if ($extension === 'csv') {
                            $rows = array_map('str_getcsv', file($path));
                        } else {
                            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                            $sheet = $spreadsheet->getActiveSheet();
                            $rows = $sheet->toArray();
                        }

                        // প্রথম row যদি header হয়, skip করো
                        if (isset($rows[0]) && str_contains(strtolower(implode(',', $rows[0])), 'phone')) {
                            array_shift($rows);
                        }

                        $count = 0;
                        foreach ($rows as $row) {
                            $phone = trim($row[0] ?? null);
                            if (!$phone) continue;

                            //  lead table-এ insert/update
                            // $lead = Lead::firstOrCreate(['phone' => $phone]);
                            $lead = Lead::firstOrCreate(
                                ['phone' => $phone],
                                [
                                    'user_id' => auth()->id(),
                                    'name' => $row[1] ?? null,
                                    'email' => $row[2] ?? null,
                                    'source' => $row[3] ?? 'CSV Import',
                                ]
                            );
                            

                            //  pivot table (campaign_leads)-এ attach
                            $exists = DB::table('campaign_leads')
                                ->where('campaign_id', $data['campaign_id'])
                                ->where('lead_id', $lead->id)
                                ->exists();

                            if (!$exists) {
                                CampaignLead::create([
                                    'campaign_id' => $data['campaign_id'],
                                    'lead_id' => $lead->id,
                                    'status' => 'pending',
                                ]);
                                $count++;
                            }
                        }

                        return redirect()->back()->with('notification', [
                            'title' => 'Upload Successful',
                            'body' => " {$count} leads uploaded successfully!"
                        ]);
                    })
            ]);
    }

    // 🔒 Non-admin user শুধুমাত্র নিজের campaign leads দেখতে পাবে
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
        return [];
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

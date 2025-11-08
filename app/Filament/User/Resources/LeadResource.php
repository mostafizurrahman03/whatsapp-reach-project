<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\LeadResource\Pages;
use App\Filament\User\Resources\LeadResource\RelationManagers\RecipientsRelationManager;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Enums\FiltersLayout;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Lead Lists';
    protected static ?string $pluralLabel = 'Lead Lists';
    protected static ?string $modelLabel = 'Lead';
    protected static ?string $navigationGroup = 'Campaign Management';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        // Left Column: Lead Details
                        Section::make('Details')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->maxLength(150)
                                    ->nullable(),

                                TextInput::make('source')
                                    ->label('Source')
                                    ->maxLength(100)
                                    ->nullable(),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'new' => 'New',
                                        'contacted' => 'Contacted',
                                        'converted' => 'Converted',
                                        'lost' => 'Lost',
                                    ])
                                    ->default('new')
                                    ->required(),
                            ]),

                        //  Right Column: Recipients
                        Section::make('Recipients')
                            ->description('Add recipients manually or upload from a CSV file.')
                            ->columnSpan(1)
                            ->schema([
                                // Input method selection
                                Radio::make('input_method')
                                    ->label('Select Input Method')
                                    ->options([
                                        'manual' => 'Manual Entry',
                                        'csv' => 'Upload CSV File',
                                    ])
                                    ->default('manual')
                                    ->inline()
                                    ->reactive(),

                                // Manual phone input
                                TagsInput::make('phone')
                                    ->label('Phone Numbers')
                                    ->placeholder('8801XXXXXXXXX')
                                    ->required(fn ($get) => $get('input_method') === 'manual')
                                    ->separator(',')
                                    ->helperText('Enter one or more phone numbers separated by commas.')
                                    ->visible(fn ($get) => $get('input_method') === 'manual'),

                                // CSV upload
                                FileUpload::make('phone_csv')
                                    ->label('Upload CSV of Phone Numbers')
                                    ->helperText('Upload a CSV file containing phone numbers in one column.')
                                    ->disk('public')
                                    ->directory('leads')
                                    ->acceptedFileTypes(['text/csv', 'text/plain'])
                                    ->maxSize(2048)
                                    ->required(fn ($get) => $get('input_method') === 'csv')
                                    ->visible(fn ($get) => $get('input_method') === 'csv'),

                                // Sample CSV download link (custom Blade view)
                                ViewField::make('sample_csv_link')
                                    ->view('filament.user.pages.sample-csv-link')
                                    ->visible(fn ($get) => $get('input_method') === 'csv'),
                            ]),
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
                    ->visible(fn() => auth()->user()->role === 'admin'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                // Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('source')->searchable(),
                Tables\Columns\TextColumn::make('status')->sortable()->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(),
            ])
            ->defaultSort('created_at', direction: 'desc')
            ->filters([

                // Name Filter
                Tables\Filters\SelectFilter::make('name')
                    ->label('Name')
                    ->options(
                        Lead::pluck('name', 'name') // key & value same
                    )
                    ->searchable(), // dropdown search enabled
                Tables\Filters\SelectFilter::make('source')
    ->label('Source')
    ->options(
        Lead::query()
            ->whereNotNull('source')
            ->distinct()
            ->pluck('source', 'source')
    )
    ->searchable(),


                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new'=>'New',
                        'contacted'=>'Contacted',
                        'converted'=>'Converted',
                        'lost'=>'Lost',
                    ]),
                // Date Range Filter
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    

    public static function getRelations(): array
    {
        return [
            
            //
        ];
    }

    // Multi-user query: only own leads for non-admins
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if(auth()->user()->role !== 'admin'){
            $query->where('user_id', auth()->id());
        }
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}

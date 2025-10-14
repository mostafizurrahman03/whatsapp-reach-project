<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\LeadResource\Pages;
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
                Grid::make(2) // 2-column grid
                    ->schema([
                        // Left Column: Lead Details
                        Section::make('Details')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->maxLength(150)
                                    ->nullable(),

                                // TextInput::make('email')
                                //     ->label('Email')
                                //     ->email()
                                //     ->maxLength(150)
                                //     ->nullable(),

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

                        // Right Column: Recipients (multiple phone numbers)
                        Section::make('Recipients')
                            ->columnSpan(1)
                            ->schema([
                                TagsInput::make('phone')
                                    ->label('Phone Numbers')
                                    ->placeholder('8801XXXXXXXXX')
                                    ->required()
                                    ->separator(','),

                                FileUpload::make('phone_csv')
                                    ->label('Upload CSV of Phone Numbers')
                                    ->helperText('Upload a CSV file containing phone numbers in one column.')
                                    ->disk('public')
                                    ->directory('leads')
                                    ->acceptedFileTypes(['text/csv', 'text/plain'])
                                    ->maxSize(2048),
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
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new'=>'New',
                        'contacted'=>'Contacted',
                        'converted'=>'Converted',
                        'lost'=>'Lost',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContactInformationResource\Pages;
use App\Models\ContactInformation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ContactInformationResource extends Resource
{
    protected static ?string $model = ContactInformation::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationGroup = 'Website Settings';
    protected static ?string $navigationLabel = 'Contact Information';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contact Details')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->maxLength(50)
                            ->nullable(),

                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->nullable(),

                        //  Changed from TextInput to Textarea for multiline hours
                        Forms\Components\Textarea::make('business_hours')
                            ->label('Business Hours')
                            ->rows(3)
                            ->placeholder("Mon - Fri: 9:00 AM - 6:00 PM\nSat - Sun: 10:00 AM - 4:00 PM")
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('address')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->address),

                Tables\Columns\TextColumn::make('business_hours')
                    ->label('Business Hours')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->business_hours),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Infolist for the View page
 public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('📞 Contact Details')
                    ->description('Business contact and office hour information')
                    ->schema([
                        // Use grid for clean 2-column layout
                        \Filament\Infolists\Components\Grid::make(2)
                            ->schema([
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope')
                                    ->color('primary')
                                    ->copyable()
                                    ->columnSpan(1),

                                TextEntry::make('phone')
                                    ->label('Phone')
                                    ->icon('heroicon-o-phone')
                                    ->color('primary')
                                    ->copyable()
                                    ->columnSpan(1),

                                TextEntry::make('address')
                                    ->label('Address')
                                    ->icon('heroicon-o-map-pin')
                                    ->columnSpanFull()
                                    ->extraAttributes([
                                        'class' => 'text-gray-700 text-sm leading-relaxed',
                                    ]),

                                TextEntry::make('business_hours')
                                    ->label('Business Hours')
                                    ->icon('heroicon-o-clock')
                                    ->formatStateUsing(fn($state) => nl2br(e($state)))
                                    ->html()
                                    ->columnSpanFull()
                                    ->extraAttributes([
                                        'class' => 'text-gray-700 text-sm leading-relaxed bg-gray-50 rounded-lg p-3 border border-gray-100',
                                    ]),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(), // allows section to collapse/expand
            ]);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactInformation::route('/'),
            'create' => Pages\CreateContactInformation::route('/create'),
            'edit' => Pages\EditContactInformation::route('/{record}/edit'),
        ];
    }
}

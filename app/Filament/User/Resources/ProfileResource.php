<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ProfileResource\Pages;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Group;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Profile';
    protected static ?string $pluralLabel = 'Profiles';
    protected static ?string $modelLabel = 'Profile';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?int $navigationSort = 6;

    // Form schema (Create / Edit)
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User')
                    ->required(),
                Forms\Components\Textarea::make('bio')
                    ->label('Bio')
                    ->rows(3),
                Forms\Components\FileUpload::make('profile_picture')
                    ->label('Profile Picture')
                    ->image()
                    ->directory('profile_pictures'),
                Forms\Components\Toggle::make('is_online')
                    ->label('Online Status'),
                Forms\Components\DateTimePicker::make('last_seen')
                    ->label('Last Seen'),
            ]);
    }

    // Table schema (List)
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('User')->sortable(),
                Tables\Columns\TextColumn::make('bio')->limit(30),
                Tables\Columns\ImageColumn::make('profile_picture')->label('Picture'),
                Tables\Columns\IconColumn::make('is_online')
                    ->boolean()
                    ->label('Online'),
                Tables\Columns\TextColumn::make('last_seen')
                    ->dateTime('d M Y H:i')
                    ->label('Last Seen'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    // Infolist schema for view page
    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make('👤 Profile Information')
                ->description('Complete details of the selected user profile')
                ->schema([
                    Grid::make(3)->schema([

                        // প্রোফাইল ছবি (Left)
                        ImageEntry::make('profile_picture')
                            ->label('')
                            ->circular()
                            ->height(180)
                            ->columnSpan(1),

                        // ইউজারের ডিটেইলস (Middle)
                        Group::make([
                            TextEntry::make('user.name')
                                ->label('Full Name')
                                ->weight('bold')
                                ->size('xl')
                                ->icon('heroicon-o-user'),

                            TextEntry::make('bio')
                                ->label('Bio')
                                ->default('No bio available')
                                ->placeholder('No bio available')
                                ->columnSpanFull()
                                ->icon('heroicon-o-chat-bubble-left-ellipsis'),
                        ])
                        ->columnSpan(1),

                        // Online / Last Seen (Right)
                        Group::make([
                            IconEntry::make('is_online')
                                ->boolean()
                                ->label('Online Status')
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle'),

                            TextEntry::make('last_seen')
                                ->label('Last Seen')
                                ->dateTime('d M Y, h:i A')
                                ->badge()
                                ->color('gray'),
                        ])
                        ->columnSpan(1),
                    ]),
                ])
                ->columns(3)
                ->collapsible(),

            Section::make('📅 Timestamps')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d M Y, h:i A')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('d M Y, h:i A')
                            ->badge()
                            ->color('warning'),
                    ]),
                ])
                ->columns(2)
                ->collapsed(),
        ]);
}


    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProfiles::route('/'),
            'view'   => Pages\ViewProfile::route('/{record}'),
            'create' => Pages\CreateProfile::route('/create'),
            'edit'   => Pages\EditProfile::route('/{record}/edit'),
        ];
    }
}

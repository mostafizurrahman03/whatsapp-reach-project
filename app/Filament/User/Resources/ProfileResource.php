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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Profile';
    protected static ?string $pluralLabel = 'Profiles';
    protected static ?string $modelLabel = 'Profile';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?int $navigationSort = 6;

    /**
     * Only logged-in user's profile will be queried
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    // ---------------- FORM ----------------
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => Auth::id()),

                // Users table fields
                Forms\Components\TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->maxLength(255),

                // Profile table fields
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

    // ---------------- TABLE ----------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_picture')->label('Picture'),
                Tables\Columns\TextColumn::make('name')->label('Name')->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('bio')->limit(30),
                Tables\Columns\IconColumn::make('is_online')->boolean()->label('Online'),
                Tables\Columns\TextColumn::make('last_seen')->dateTime('d M Y H:i')->label('Last Seen'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->actionsColumnLabel('Action')
            ->bulkActions([]);
    }

    // ---------------- INFOLIST ----------------
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Profile Information')
                    ->icon('heroicon-o-user')
                    ->description('Complete details of your profile')
                    ->schema([
                        Grid::make(3)->schema([

                            // Left: Profile Picture
                            ImageEntry::make('profile_picture')
                                ->label('')
                                ->circular()
                                ->height(180)
                                ->columnSpan(1),

                            // Middle: Name, Email, Bio
                            Group::make([
                                TextEntry::make('name')
                                    ->label('Full Name')
                                    ->weight('bold')
                                    ->size('xl')
                                    ->icon('heroicon-o-user')
                                    ->getStateUsing(fn ($record) => $record->user->name ?? ''),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope')
                                    ->getStateUsing(fn ($record) => $record->user->email ?? ''),

                                TextEntry::make('bio')
                                    ->label('Bio')
                                    ->default('No bio available')
                                    ->placeholder('No bio available')
                                    ->columnSpanFull()
                                    ->icon('heroicon-o-chat-bubble-left-ellipsis'),
                            ])->columnSpan(1),

                            // Right: Online & Last Seen
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
                            ])->columnSpan(1),
                        ]),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Timestamps')
                    ->icon('heroicon-o-calendar')
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

    // Navigation to view page
    public static function getNavigationUrl(): string
    {
        // Get current user's profile
        $profile = Profile::where('user_id', auth()->id())->first();
        
        if ($profile) {
            return static::getUrl('view', ['record' => $profile->id]);
        }
        
        // If no profile exists, go to create page
        return static::getUrl('create');
    }

    public static function shouldSkipAuthorization(): bool
    {
        return true;
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
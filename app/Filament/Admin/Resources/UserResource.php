<?php

// namespace App\Filament\Resources;
namespace App\Filament\Admin\Resources;


use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
// use Spatie\Permission\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
    ->schema([
        Forms\Components\TextInput::make('name')
            ->required()
            ->maxLength(255),

        Forms\Components\TextInput::make('email')
            ->email()
            ->required()
            ->maxLength(255),

        Forms\Components\DateTimePicker::make('email_verified_at'),

        Forms\Components\TextInput::make('password')
            ->password()
            ->required(fn ($record) => $record === null) // update password optional
            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
            ->maxLength(255),

        //  Role Assign Field
        Forms\Components\Select::make('roles')
            ->label('Assign Role')
            ->multiple()
            ->relationship('roles', 'name') // Spatie Role relation
            ->preload()
            ->required(),
    ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('primary'),
                Tables\Columns\TextColumn::make('roles.name')
                ->label('Roles')
                ->sortable()
                ->wrap(),
                
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_user');
    }

    

}

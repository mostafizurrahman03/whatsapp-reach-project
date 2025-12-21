<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Spatie
use Spatie\Permission\Traits\HasRoles;

// Filament
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // FILAMENT ACCESS (v3)
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin'  => $this->hasAnyRole(['admin', 'super_admin']),
            'user' => $this->hasRole('user'),
            default  => false,
        };
    }

    public function myWhatsappDevices()
    {
        return $this->hasMany(MyWhatsappDevice::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function templates()
    {
        return $this->hasMany(MessageTemplate::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}

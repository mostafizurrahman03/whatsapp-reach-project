<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'profile_picture',
        'last_seen',
        'is_online',
    ];

    // relation with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


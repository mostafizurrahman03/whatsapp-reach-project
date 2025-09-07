<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulkMediaMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'user_id',
        'message',
        'caption',
        'media_url',
        'is_sent',
    ];

    public function recipients()
    {
        return $this->hasMany(BulkMediaMessageRecipient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

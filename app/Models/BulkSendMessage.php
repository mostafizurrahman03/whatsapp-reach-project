<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulkSendMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'user_id',
        'message',
        'is_sent',
    ];

    public function recipients()
    {
        return $this->hasMany(BulkSendMessageRecipient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

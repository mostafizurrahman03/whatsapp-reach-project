<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulkMediaMessageRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulk_media_message_id',
        'number',
        'message',
        'media_url',
        'caption',
        'is_sent',
    ];

    public function bulkMediaMessage()
    {
        return $this->belongsTo(BulkMediaMessage::class);
    }
    public function message()
    {
        return $this->belongsTo(BulkMediaMessage::class, 'bulk_media_message_id');
    }
}

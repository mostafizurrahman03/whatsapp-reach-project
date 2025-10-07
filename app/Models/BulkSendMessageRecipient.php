<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulkSendMessageRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulk_send_message_id',
        'number',
        'sent_at',
        'is_sent',
    ];

    // public function bulkMessage()
    // {
    //     return $this->belongsTo(BulkSendMessage::class);
    // }
    protected $casts = [
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
    ];
    public function bulkSendMessage()
    {
        return $this->belongsTo(BulkSendMessage::class);
    }

}

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
        'is_sent',
        'sent_at',
    ];

    // public function bulkMessage()
    // {
    //     return $this->belongsTo(BulkSendMessage::class);
    // }
    protected $casts = [
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];
    public function bulkSendMessage()
    {
        return $this->belongsTo(BulkSendMessage::class, 'bulk_send_message_id');
    }
    protected $table = 'bulk_send_message_recipients';

    // Campaign relation
    public function campaign()
    {
        return $this->belongsTo(Campaign::class,'campaign_id','id');
    }

   

}

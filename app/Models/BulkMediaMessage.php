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


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Device relation
    public function device()
    {
        return $this->belongsTo(MyWhatsappDevice::class, 'device_id', 'device_id');
    }
    

    // Recipients relation
    public function recipients()
    {
        return $this->hasMany(BulkMediaMessageRecipient::class, 'bulk_media_message_id');
    }

}

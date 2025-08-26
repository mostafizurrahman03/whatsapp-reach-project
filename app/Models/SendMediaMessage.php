<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendMediaMessage extends Model
{
        protected $fillable = [
        'number',
        'device_id',
        'message',
        'caption',
        'media_url',
        'is_sent',
    ];
    public function device()
    {
        return $this->belongsTo(\App\Models\MyWhatsappDevice::class, 'device_id', 'device_id');
    }

        

}

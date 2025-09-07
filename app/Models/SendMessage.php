<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendMessage extends Model
{
       protected $fillable = [
        'number',
        'user_id',
        'device_id',
        'message',
        'is_sent',
    ];
    public function device()
    {
        return $this->belongsTo(\App\Models\MyWhatsappDevice::class, 'device_id', 'device_id');
    }
}

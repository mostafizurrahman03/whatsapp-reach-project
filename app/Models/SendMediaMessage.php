<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendMediaMessage extends Model
{
        protected $fillable = [
        'number',
        'message',
        'media_url',
        'is_sent',
    ];
        

}

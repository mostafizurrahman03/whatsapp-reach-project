<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendMessage extends Model
{
       protected $fillable = [
        'number',
        'message',
        'is_sent',
    ];
}

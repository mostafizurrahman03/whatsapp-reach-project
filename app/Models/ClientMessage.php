<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientMessage extends Model
{
    use HasFactory;
    protected $table = 'client_messages';
    


    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
    ];
}

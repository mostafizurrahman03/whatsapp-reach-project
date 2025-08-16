<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyWhatsappDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'phone_number',
        'qr_code',
        'qr_image',
        'session_data',
        'status',
        'connected',
        'last_connected_at',
        'last_disconnected_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

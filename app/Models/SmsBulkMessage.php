<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsBulkMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service',
        'content',
        'recipients',
        'status',
        'response',
    ];

    protected $casts = [
        'recipients' => 'array',
        'response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

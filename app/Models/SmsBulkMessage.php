<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsBulkMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'vendor_configuration_id',
        'sender_id',
        'content',
        'recipients',
        'status',
        'response',
        'total_recipients',
        'success_count',
        'failed_count',
        'cost',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'response' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function vendorConfiguration()
    {
        return $this->belongsTo(VendorConfiguration::class);
    }

    public function sender()
    {
        return $this->belongsTo(SmsSenderId::class, 'sender_id');
    }
}

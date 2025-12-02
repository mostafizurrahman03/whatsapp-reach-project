<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorConfiguration extends Model
{
    // Table name (optional but best practice)
    protected $table = 'vendor_configurations';

    // Mass assignable fields
    protected $fillable = [
        'service_id',
        'vendor_name',
        'api_key',
        'secret_key',
        'base_url',
        'tps',
        'extra_config',
        'is_active',
    ];

    // Casts for correct data types
    protected $casts = [
        'extra_config' => 'array',
        'is_active' => 'boolean',
        'tps' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Each vendor is linked to a service (SMS/WhatsApp/Voice/etc)
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

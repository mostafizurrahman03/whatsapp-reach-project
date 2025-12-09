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
        'base_url',
        'api_key',
        'secret_key',
        'tps',
        'extra_config',
        'ip_whitelist',
        'is_active',
    ];

    // Casts for correct data types
    protected $casts = [
        'extra_config' => 'array',
        'ip_whitelist' => 'array',
        'is_active' => 'boolean',
    ];

    // Each vendor is linked to a service (SMS/WhatsApp/Voice/etc)
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientConfiguration extends Model
{
    // Table name (optional default, but best practice)
    protected $table = 'client_configurations';

    // Mass assignable fields
    protected $fillable = [
        'client_name',
        'api_key',
        'secret_key',
        'tps',
        'service_routing',
    ];

    // Casts for correct data types
    protected $casts = [
        'tps' => 'integer',
        'service_routing' => 'array', // {"sms":"reve","whatsapp":"meta"}
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships (future-ready)
     |--------------------------------------------------------------------------
     |
     | You may want to track which client is using which services.
     | For now, skipped because no direct table relation.
     |
     */

    // Example (if needed in future)
    // public function usageLogs()
    // {
    //     return $this->hasMany(ClientUsageLog::class);
    // }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ClientConfiguration extends Model
{
    protected $table = 'client_configurations';

    protected $fillable = [
        'user_id',
        'client_api_key',
        'client_secret_key',
        'balance',
        'rate_per_sms',
        'tps',
        'service_routing',
        'allowed_ips',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'rate_per_sms' => 'decimal:2',
        'tps' => 'integer',
        'service_routing' => 'array',
        'allowed_ips' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** RELATIONSHIPS **/

    // Each client config belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
     * IMPORTANT NOTE:
     * No "service()" relationship exists in your migration.
     * There is NO "service_id" column in your DB.
     * So service() relationship removed.
     */
}

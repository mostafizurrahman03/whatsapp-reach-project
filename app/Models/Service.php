<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    // Table name
    protected $table = 'services';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    // Casts (future friendly)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

   

    // Example: A service can have many vendor configurations
    public function vendorConfigurations()
    {
        return $this->hasMany(VendorConfiguration::class);
    }

    // Example: A service can have many client configurations
    public function clientConfigurations()
    {
        return $this->hasMany(ClientConfiguration::class);
    }
}


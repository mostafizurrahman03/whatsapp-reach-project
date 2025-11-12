<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactInformation extends Model
{
     use HasFactory;

    protected $table = 'contact_information';

    /**
     * mass assignable
     */
    protected $fillable = [
        'email',
        'phone',
        'address',
        'business_hours',
    ];
}

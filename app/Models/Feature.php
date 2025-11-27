<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
     use HasFactory;

    protected $fillable = [
        'icon',
        'title',
        'short_description',
        'items',
        'sort_order',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}



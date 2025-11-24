<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $table = 'pricing_plans';

    protected $fillable = [
        'name',
        'slug',
        'monthly_price',
        'yearly_price',
        'description',
        'is_popular',
        'features',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'status' => 'boolean',
        'features' => 'array', // JSON → array
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
    ];
}

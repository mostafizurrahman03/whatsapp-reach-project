<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageTemplate extends Model
{
    use HasFactory;

    // Fillable fields
    protected $fillable = [
        'name',
        'content',
        'type',
        'created_by',
    ];

    /**
     * Relation: Template creator (User)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'template_id');
    }
}




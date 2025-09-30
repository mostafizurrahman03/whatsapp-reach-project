<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MessageTemplate extends Model
{
    use HasFactory;

    // Fillable fields
    protected $fillable = [
        'user_id',
        'name',
        'content',
        'type',
        
    ];

    /**
     * Relation: Template creator (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'template_id');
    }
}




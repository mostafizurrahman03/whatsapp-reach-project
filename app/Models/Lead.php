<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'source',
        'status'
    ];

    /**
     * Lead এর owner user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lead যে campaign-এ আছে
     */
    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_leads')
                    ->withPivot('status', 'sent_at')
                    ->withTimestamps();
    }
     // Recipients relation
    public function recipients()
    {
        return $this->hasMany(LeadResource::class, 'lead_id');
    }

}

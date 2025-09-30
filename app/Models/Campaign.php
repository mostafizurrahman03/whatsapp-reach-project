<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'template_id',
        'channel',
        'status',
        'scheduled_at',
        
    ];

    /**
     * Relation: Template used in this campaign
     */
    public function template()
    {
        return $this->belongsTo(MessageTemplate::class, 'template_id');
    }

    /**
     * Relation: User who created the campaign
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'campaign_leads')
                    ->withPivot('status','sent_at')
                    ->withTimestamps();
    }
}







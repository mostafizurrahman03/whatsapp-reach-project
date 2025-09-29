<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'template_id',
        'channel',
        'status',
        'scheduled_at',
        'created_by',
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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'campaign_leads')
                    ->withPivot('status','sent_at')
                    ->withTimestamps();
    }
}







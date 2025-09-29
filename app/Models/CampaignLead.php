<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaignLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'lead_id',
        'status',
        'sent_at',
    ];

    /**
     * Relation: Campaign
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Relation: Lead
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}

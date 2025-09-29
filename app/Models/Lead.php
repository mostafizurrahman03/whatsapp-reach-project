<?php

class Lead extends Model
{
    use HasFactory;

    protected $fillable = ['name','phone','email','source','status'];

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_leads')
                    ->withPivot('status', 'sent_at')
                    ->withTimestamps();
    }
}


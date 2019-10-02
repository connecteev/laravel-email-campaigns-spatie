<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignOpen extends Model
{
    protected $guarded = [];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'email_campaign_id');
    }
}

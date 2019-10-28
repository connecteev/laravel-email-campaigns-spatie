<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignClick extends Model
{
    protected $guarded = [];

    public function link(): BelongsTo
    {
        return $this->belongsTo(CampaignLink::class, 'campaign_link_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class, 'email_list_subscriber_id');
    }
}

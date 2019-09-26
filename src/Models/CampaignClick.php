<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use MongoDB\Driver\Monitoring\Subscriber;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignClick extends Model
{
    protected $guarded = [];

    public function link(): BelongsTo
    {
        return $this->belongsTo(CampaignLink::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
}

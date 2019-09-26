<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use MongoDB\Driver\Monitoring\Subscriber;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailListSubscription extends Model
{
    public $guarded = [];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class, 'email_list_subscriber_id');
    }
}

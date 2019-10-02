<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailCampaignSend extends Model
{
    public $guarded = [];

    public $dates = ['sent_at'];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'email_list_subscription_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'email_campaign_id');
    }

    public function markAsSent()
    {
        $this->sent_at = now();

        $this->save();

        return $this;
    }

    public function wasAlreadySent(): bool
    {
        return ! is_null($this->sent_at);
    }
}

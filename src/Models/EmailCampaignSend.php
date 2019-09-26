<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailCampaignSend extends Model
{
    public $dates = ['sent_at'];

    public function emailListSubscriber(): BelongsTo
    {
        return $this->belongsTo(EmailListSubscriber::class);
    }

    public function emailCampaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class);
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

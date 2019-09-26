<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EmailCampaigns\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EmailCampaigns\Http\Controllers\TrackClicksController;

class CampaignLink extends Model
{
    use HasUuid;

    protected $guarded = [];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(CampaignClick::class);
    }

    public function registerClick(EmailListSubscriber $subscriber)
    {
        $this->clicks()->create([
            'email_subscriber_id' => $subscriber->id,
        ]);
    }

    public function getUrlAttribute()
    {
        return url(action(TrackClicksController::class, $this->uuid)).'[[subscriberUuid]]?redirect='.urlencode($this->original_link);
    }
}

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

    public $casts = [
        'click_count' => 'integer',
        'unique_click_count' => 'integer',
    ];

    protected $guarded = [];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'email_campaign_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(CampaignClick::class);
    }

    public function registerClick(Subscriber $subscriber)
    {
        $this->clicks()->create([
            'email_list_subscriber_id' => $subscriber->id,
        ]);
    }

    public function getUrlAttribute()
    {
        return url(action(TrackClicksController::class, [$this->uuid, '::subscriber.uuid::'])).'?redirect='.urlencode($this->original_link);
    }
}

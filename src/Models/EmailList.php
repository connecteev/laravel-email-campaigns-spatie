<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmailList extends Model
{
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(EmailListSubscriber::class, 'email_list_subscriptions');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(EmailListSubscription::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class);
    }
}

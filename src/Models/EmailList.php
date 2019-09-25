<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class EmailList extends Model
{
    public function subscribers(): HasManyThrough
    {
        return $this->hasManyThrough(EmailListSubscriber::class, EmailListSubscription::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class);
    }
}


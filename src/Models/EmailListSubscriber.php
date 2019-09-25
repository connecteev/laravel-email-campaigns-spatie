<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\EmailCampaigns\Models\Concerns\HasUuid;

class EmailListSubscriber extends Model
{
    use HasUuid;

    protected $guarded = [];

    public function emailLists(): HasManyThrough
    {
        return $this->hasManyThrough(EmailList::class, EmailListSubscription::class);
    }
}


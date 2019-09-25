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

    public function subscribeTo(EmailList $emailList): self
    {
        EmailListSubscription::firstOrCreate([
            'email_list_subscriber_id' => $this->id,
            'email_list_id' => $emailList->id,
        ]);

        return $this->fresh();
    }

    public function isSubscribedTo(EmailList $emailList): bool
    {
        return EmailListSubscription::query()
            ->where('email_list_subscriber_id', $this->id)
            ->where('email_list_id', $emailList->id)
            ->exists();
    }
}


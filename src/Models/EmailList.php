<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EmailCampaigns\Enums\EmailListSubscriptionStatus;

class EmailList extends Model
{
    public $guarded = [];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'email_list_subscriptions', 'email_list_id',  'email_list_subscriber_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(EmailListSubscription::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class);
    }

    public function subscribe(string $email): EmailListSubscription
    {
        /** @var \Spatie\EmailCampaigns\Models\Subscriber $subscriber */
        $subscriber = Subscriber::firstOrCreate([
            'email' => $email,
        ]);

        return $subscriber->subscribeTo($this);
    }

    public function isSubscribed(string $email): bool
    {
        if (!$subscriber = Subscriber::findForEmail($email)) {
            return false;
        };

        if (!$subscription = $this->getSubscription($subscriber)) {
            return false;
        };

        return $subscription->status === EmailListSubscriptionStatus::SUBSCRIBED;
    }

    public function getSubscription(Subscriber $subscriber): ?EmailListSubscription
    {
        return EmailListSubscription::query()
            ->where('email_list_id', $this->id)
            ->where('email_list_subscriber_id', $subscriber->id)
            ->first();
    }
}

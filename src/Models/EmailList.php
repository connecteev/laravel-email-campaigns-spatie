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

    public function subscribe(string $email): EmailListSubscription
    {
        /** @var \Spatie\EmailCampaigns\Models\EmailListSubscriber $subscriber */
        $subscriber = EmailListSubscriber::firstOrCreate([
            'email' => $email,
        ]);

        return $subscriber->subscribeTo($this);
    }

    public function isSubscribed(string $email): bool
    {
        if (!$subscriber = EmailListSubscriber::findForEmail($email)) {
            return false;
        };

        if (!$subscription = $this->getSubscription($subscriber)) {
            return false;
        };

        return $subscription->status === EmailListSubscriptionStatus::SUBSCRIBED;
    }

    public function getSubscription(EmailListSubscriber $subscriber): ?EmailListSubscription
    {
        return EmailListSubscription::query()
            ->where('email_list_id', $this->id)
            ->where('email_list_subscriber_id', $subscriber->id)
            ->first();
    }
}

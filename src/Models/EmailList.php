<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmailList extends Model
{
    public $guarded = [];

    public function subscribers(): BelongsToMany
    {
        return $this->allSubscribers()->wherePivot('status', SubscriptionStatus::SUBSCRIBED);
    }

    public function allSubscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'email_list_subscriptions', 'email_list_id', 'email_list_subscriber_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->where('status', SubscriptionStatus::SUBSCRIBED);
    }

    public function allSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function subscribe(string $email): Subscription
    {
        /** @var \Spatie\EmailCampaigns\Models\Subscriber $subscriber */
        $subscriber = Subscriber::firstOrCreate([
            'email' => $email,
        ]);

        return $subscriber->subscribeTo($this);
    }

    public function isSubscribed(string $email): bool
    {
        if (! $subscriber = Subscriber::findForEmail($email)) {
            return false;
        }

        if (! $subscription = $this->getSubscription($subscriber)) {
            return false;
        }

        return $subscription->status === SubscriptionStatus::SUBSCRIBED;
    }

    public function getSubscription(Subscriber $subscriber): ?Subscription
    {
        return Subscription::query()
            ->where('email_list_id', $this->id)
            ->where('email_list_subscriber_id', $subscriber->id)
            ->first();
    }

    public function unsubscribe(string $email): bool
    {
        if (! $subscriber = Subscriber::findForEmail($email)) {
            return false;
        }

        if (! $subscription = $this->getSubscription($subscriber)) {
            return false;
        }

        $subscription->markAsUnsubscribed();

        return true;
    }
}

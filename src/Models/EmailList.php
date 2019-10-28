<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Spatie\EmailCampaigns\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Exceptions\CouldNotSubscribe;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmailList extends Model
{
    use HasUuid;

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

    public function subscribe(string $email, array $attributes = [], array $extraAttributes = []): Subscription
    {
        $subscriber = $this->createSubscriber($email, $attributes, $extraAttributes);

        return $subscriber->subscribeTo($this);
    }

    public function subscribeNow(string $email, array $attributes = [], array $extraAttributes = []): Subscription
    {
        $subscriber = $this->createSubscriber($email, $attributes, $extraAttributes);

        return $subscriber->subscribeNowTo($this);
    }

    protected function createSubscriber(string $email, array $attributes = [], $extraAttributes = []): Subscriber
    {
        if (Validator::make(compact('email'), ['email' => 'email'])->fails()) {
            throw CouldNotSubscribe::invalidEmail($email);
        }

        /** @var \Spatie\EmailCampaigns\Models\Subscriber $subscriber */
        $subscriber = Subscriber::firstOrCreate([
            'email' => $email,
        ]);

        $subscriber->fill($attributes);

        $subscriber->extra_attributes = $extraAttributes;
        $subscriber->save();

        return $subscriber;
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

    public function getSubscriptionStatus(string $email): ?string
    {
        $subscription = Subscription::query()
            ->where('email_list_id', $this->id)
            ->whereHas('subscriber', function (Builder $query) use ($email) {
                $query->where('email', $email);
            })
            ->first();

        return optional($subscription)->status;
    }
}

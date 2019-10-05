<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;

class ConfirmSubscriptionController
{
    public function __invoke(string $subscriptionUuid)
    {
        /** @var \Spatie\EmailCampaigns\Models\Subscription $subscription */
        $subscription = Subscription::findByUuid($subscriptionUuid);

        if (! $subscription) {
            return $this->couldNotFindSubscriptionResponse();
        }

        if ($subscription->status === SubscriptionStatus::SUBSCRIBED) {
            return $this->wasAlreadyConfirmedResponse($subscription);
        }

        $subscription->confirm();

        return $this->subscriptionConfirmedResponse($subscription);
    }

    public function subscriptionConfirmedResponse(Subscription $subscription)
    {
        return view('email-campaigns::confirmSubscription.confirmed', compact('subscription'));
    }

    public function wasAlreadyConfirmedResponse(Subscription $subscription)
    {
        return view('email-campaigns::confirmSubscription.wasAlreadyConfirmed', compact('subscription'));
    }

    public function couldNotFindSubscriptionResponse()
    {
        return view('email-campaigns::confirmSubscription.couldNotFindSubscription');
    }
}

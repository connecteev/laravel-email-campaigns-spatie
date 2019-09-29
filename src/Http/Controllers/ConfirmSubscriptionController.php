<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Spatie\EmailCampaigns\Enums\EmailListSubscriptionStatus;
use Spatie\EmailCampaigns\Models\EmailListSubscription;

class ConfirmSubscriptionController
{
    public function __invoke(string $subscriptionUuid)
    {
        /** @var \Spatie\EmailCampaigns\Models\EmailListSubscription $subscription */
        $subscription = EmailListSubscription::findByUuid($subscriptionUuid);

        if (! $subscription) {
            return $this->couldNotFindSubscriptionResponse();
        }

        if ($subscription->status === EmailListSubscriptionStatus::SUBSCRIBED) {
            return $this->wasAlreadyConfirmedResponse($subscription);
        }

        $subscription->confirm();

        return $this->subscriptionConfirmedResponse($subscription);
    }

    public function subscriptionConfirmedResponse(EmailListSubscription $subscription)
    {
        return view('email-campaigns::confirmSubscription.confirmed', compact('subscription'));
    }

    public function wasAlreadyConfirmedResponse(EmailListSubscription $subscription)
    {
        return view('email-campaigns::confirmSubscription.wasAlreadyConfirmed', compact('subscription'));
    }

    public function couldNotFindSubscriptionResponse()
    {
        return view('email-campaigns::confirmSubscription.couldNotFindSubscription');
    }
}


<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Spatie\EmailCampaigns\Models\Subscription;

class UnsubscribeController
{
    public function __invoke(string $emailListSubscriptionUuid)
    {
        /** @var \Spatie\EmailCampaigns\Models\Subscription $subscription */
        if (! $subscription = Subscription::findByUuid($emailListSubscriptionUuid)) {
            return view('email-campaigns::unsubscribe.notFound');
        }

        $emailList = $subscription->emailList;

        $subscription->markAsUnsubscribed();

        return view('email-campaigns::unsubscribe.unsubscribed', compact('emailList'));
    }
}

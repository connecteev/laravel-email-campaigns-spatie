<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Spatie\EmailCampaigns\Models\EmailListSubscription;

class UnsubscribeController
{
    public function __construct(string $emailListSubscriptionUuid)
    {
        /** @var \Spatie\EmailCampaigns\Models\EmailListSubscription $subscription */
        if (! $subscription = EmailListSubscription::findByUuid($emailListSubscriptionUuid)) {
            return view('email-campaigns::unsubscribe.notFound');
        };

        $emailList = $subscription->emailList;

        $subscription->delete();

        return view('email-campaigns::unsubscribe.unsubscribed', compact('emailList'));
    }
}


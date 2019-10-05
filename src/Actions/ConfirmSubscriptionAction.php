<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Events\Subscribed;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;

class ConfirmSubscriptionAction
{
    public function execute(Subscription $subscription)
    {
        $subscription->update(['status' => SubscriptionStatus::SUBSCRIBED]);

        event(new Subscribed($subscription));
    }
}

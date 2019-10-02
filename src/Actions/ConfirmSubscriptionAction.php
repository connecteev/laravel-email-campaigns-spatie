<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Models\Subscription;

class ConfirmSubscriptionAction
{
    public function execute(Subscription $emailListSubscription)
    {
        $emailListSubscription->update(['status' => SubscriptionStatus::SUBSCRIBED]);
    }
}


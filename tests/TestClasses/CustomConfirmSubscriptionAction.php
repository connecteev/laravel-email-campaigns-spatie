<?php

namespace Spatie\EmailCampaigns\Tests\TestClasses;

use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Actions\ConfirmSubscriptionAction;

class CustomConfirmSubscriptionAction extends ConfirmSubscriptionAction
{
    public function execute(Subscription $subscription)
    {
        $subscription->subscriber->update(['email' => 'overridden@example.com']);

        parent::execute($subscription);
    }
}

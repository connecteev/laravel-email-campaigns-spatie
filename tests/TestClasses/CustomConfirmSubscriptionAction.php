<?php


namespace Spatie\EmailCampaigns\Tests\TestClasses;


use Spatie\EmailCampaigns\Actions\ConfirmSubscriptionAction;
use Spatie\EmailCampaigns\Models\Subscription;

class CustomConfirmSubscriptionAction extends ConfirmSubscriptionAction
{
    public function execute(Subscription $subscription)
    {
        $subscription->subscriber->update(['email' => 'overridden@example.com']);

        parent::execute($subscription);
    }
}

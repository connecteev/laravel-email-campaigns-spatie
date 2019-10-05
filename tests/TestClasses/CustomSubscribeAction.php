<?php

namespace Spatie\EmailCampaigns\Tests\TestClasses;

use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Actions\SubscribeAction;

class CustomSubscribeAction extends SubscribeAction
{
    public function execute(Subscriber $subscriber, EmailList $emailList): Subscription
    {
        $subscriber->update(['email' => 'overridden@example.com']);

        return parent::execute($subscriber, $emailList);
    }
}

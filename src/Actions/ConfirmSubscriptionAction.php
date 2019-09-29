<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Enums\EmailListSubscriptionStatus;
use Spatie\EmailCampaigns\Models\EmailListSubscription;

class ConfirmSubscriptionAction
{
    public function execute(EmailListSubscription $emailListSubscription)
    {
        $emailListSubscription->update(['status' => EmailListSubscriptionStatus::SUBSCRIBED]);
    }
}


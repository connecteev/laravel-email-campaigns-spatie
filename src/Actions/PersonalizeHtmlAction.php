<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;
use Spatie\EmailCampaigns\Models\EmailListSubscription;

class PersonalizeHtmlAction
{
    public function handle($html, EmailListSubscription $emailListSubscription, EmailCampaign $emailCampaign)
    {
        $html = str_replace(urlencode('[[subscriptionUuid]]'), $emailListSubscription->uuid, $html);
        $html = str_replace(urlencode('[[subscriberUuid]]'), $emailListSubscription->subscriber->uuid, $html);

        return $html;
    }
}

<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Http\Controllers\UnsubscribeController;
use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Models\EmailListSubscription;

class PersonalizeHtmlAction
{
    public function handle($html, EmailListSubscription $emailListSubscription, EmailCampaign $emailCampaign)
    {
        $html = str_replace('@@subscriptionUuid@@', $emailListSubscription->uuid, $html);
        $html = str_replace('@@subscriberUuid@@', $emailListSubscription->subscriber->uuid, $html);
        $html = str_replace('@@unsubscribeLink@@', action(UnsubscribeController::class, $emailListSubscription->uuid), $html);

        return $html;
    }
}

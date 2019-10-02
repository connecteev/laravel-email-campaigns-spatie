<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Http\Controllers\UnsubscribeController;
use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Models\Subscription;

class PersonalizeHtmlAction
{
    public function handle($html, Subscription $emailListSubscription, EmailCampaign $emailCampaign)
    {
        $html = str_replace('@@subscriptionUuid@@', $emailListSubscription->uuid, $html);
        $html = str_replace('@@subscriberUuid@@', $emailListSubscription->subscriber->uuid, $html);
        $html = str_replace('@@unsubscribeLink@@', action(UnsubscribeController::class, $emailListSubscription->uuid), $html);

        return $html;
    }
}

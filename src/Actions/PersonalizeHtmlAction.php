<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Http\Controllers\UnsubscribeController;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Models\Subscription;

class PersonalizeHtmlAction
{
    public function execute($html, CampaignSend $pendingSend)
    {
        $subscription = $pendingSend->subscription;

        $html = str_replace('@@campaignSendUuid@@', $pendingSend->uuid, $html);
        $html = str_replace('@@subscriptionUuid@@', $subscription->uuid, $html);
        $html = str_replace('@@subscriberUuid@@', $subscription->subscriber->uuid, $html);
        $html = str_replace('@@unsubscribeLink@@', action(UnsubscribeController::class, $subscription->uuid), $html);

        return $html;
    }
}

<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Http\Controllers\UnsubscribeController;

class PersonalizeHtmlAction
{
    public function execute($html, CampaignSend $pendingSend)
    {
        $subscription = $pendingSend->subscription;

        $html = str_replace('::campaignSendUuid::', $pendingSend->uuid, $html);
        $html = str_replace('::subscriptionUuid::', $subscription->uuid, $html);
        $html = str_replace('::subscriberUuid::', $subscription->subscriber->uuid, $html);
        $html = str_replace('::unsubscribeUrl::', action(UnsubscribeController::class, $subscription->uuid), $html);

        return $html;
    }
}

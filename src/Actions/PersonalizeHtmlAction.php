<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Http\Controllers\UnsubscribeController;
use Spatie\EmailCampaigns\Models\Subscriber;

class PersonalizeHtmlAction
{
    public function execute($html, CampaignSend $pendingSend)
    {
        $subscription = $pendingSend->subscription;

        $html = str_replace('::campaignSendUuid::', $pendingSend->uuid, $html);
        $html = str_replace('::subscriptionUuid::', $subscription->uuid, $html);
        $html = str_replace('::subscriber.uuid::', $subscription->subscriber->uuid, $html);
        $html = str_replace('::unsubscribeUrl::', action(UnsubscribeController::class, $subscription->uuid), $html);

        $html = $this->replaceSubscriberAttributes($html, $subscription->subscriber);

        return $html;
    }

    protected function replaceSubscriberAttributes(string $html, Subscriber $subscriber): string
    {
        /*
        $html = str_replace('::subscriber.uuid::', $subscriber->uuid, $html);
        $html = str_replace('::subscriber.extra_attributes.first_name::', 'John', $html);
        */

        return $html;
    }
}

<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;

class UnsubscribeController
{
    public function __invoke(string $emailListSubscriptionUuid, string $campaignSendUuid = null)
    {
        /** @var \Spatie\EmailCampaigns\Models\Subscription $subscription */
        if (! $subscription = Subscription::findByUuid($emailListSubscriptionUuid)) {
            return view('email-campaigns::unsubscribe.notFound');
        }

        $emailList = $subscription->emailList;

        if ($subscription->status === SubscriptionStatus::UNSUBSCRIBED) {
            return view('email-campaigns::unsubscribe.alreadyUnsubscribed', compact('emailList'));
        }

        $campaignSend = CampaignSend::findByUuid($campaignSendUuid ?? '');
        $subscription->markAsUnsubscribed($campaignSend);

        $emailList = $subscription->emailList;

        return view('email-campaigns::unsubscribe.unsubscribed', compact('emailList'));
    }
}

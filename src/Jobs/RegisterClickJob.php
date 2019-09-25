<?php

namespace Spatie\EmailCampaigns\Jobs;

use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;

class RegisterClickJob
{
    /** @var string */
    private $campaignLinkUuid;

    /** @var string */
    private $subscriberUuid;

    public function __construct(string $campaignLinkUuid, string $subscriberUuid)
    {
        $this->campaignLinkUuid = $campaignLinkUuid;

        $this->subscriberUuid = $subscriberUuid;
    }

    public function handle()
    {
        /** @var \Spatie\EmailCampaigns\Models\CampaignLink|null $campaignLink */
        if (! $campaignLink = CampaignLink::findOrFailByUuid($this->subscriberUuid)) {
            return;
        }

        /** @var \Spatie\EmailCampaigns\Models\EmailListSubscriber|null $subscriber */
        if (! $subscriber = EmailListSubscriber::findByUuid($this->subscriberUuid)) {
            return;
        }

        $campaignLink->clicks()->create([
            'email_subscriber_id' => $subscriber->id,
        ]);
    }
}


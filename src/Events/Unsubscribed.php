<?php

namespace Spatie\EmailCampaigns\Events;

use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Models\Subscription;

class Unsubscribed
{
    /** @var \Spatie\EmailCampaigns\Models\Subscription */
    public $subscription;

    /** @var \Spatie\EmailCampaigns\Models\CampaignSend|null */
    public $campaignSend;

    public function __construct(Subscription $subscription, CampaignSend $campaignSend = null)
    {
        $this->subscription = $subscription;

        $this->campaignSend = $campaignSend;
    }
}

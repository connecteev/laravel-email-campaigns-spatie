<?php

namespace Spatie\EmailCampaigns\Events;

use Spatie\EmailCampaigns\Models\CampaignSend;

class CampaignMailSent
{
    /** @var \Spatie\EmailCampaigns\Models\CampaignSend */
    public $campaignSend;

    public function __construct(CampaignSend $campaignSend)
    {
        $this->campaignSend = $campaignSend;
    }
}

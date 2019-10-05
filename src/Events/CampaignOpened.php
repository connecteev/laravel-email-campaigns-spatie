<?php

namespace Spatie\EmailCampaigns\Events;

use Spatie\EmailCampaigns\Models\CampaignOpen;

class CampaignOpened
{
    /** @var \Spatie\EmailCampaigns\Models\CampaignOpen */
    public $campaignOpen;

    public function __construct(CampaignOpen $campaignOpen)
    {
        $this->campaignOpen = $campaignOpen;
    }
}

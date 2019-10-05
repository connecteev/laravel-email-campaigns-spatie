<?php

namespace Spatie\EmailCampaigns\Events;

use Spatie\EmailCampaigns\Models\CampaignClick;

class CampaignLinkClicked
{
    /** @var \Spatie\EmailCampaigns\Models\CampaignClick */
    public $campaignClick;

    public function __construct(CampaignClick $campaignClick)
    {
        $this->campaignClick = $campaignClick;
    }
}

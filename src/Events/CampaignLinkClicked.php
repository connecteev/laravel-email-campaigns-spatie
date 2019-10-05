<?php

namespace Spatie\EmailCampaigns\Events;

use Spatie\EmailCampaigns\Models\CampaignClick;
use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Models\Subscriber;

class CampaignLinkClicked
{
    /** @var \Spatie\EmailCampaigns\Models\CampaignClick */
    public $campaignClick;

    public function __construct(CampaignClick $campaignClick)
    {
        $this->campaignClick = $campaignClick;
    }
}

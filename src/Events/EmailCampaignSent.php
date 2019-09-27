<?php

namespace Spatie\EmailCampaigns\Events;

use Spatie\EmailCampaigns\Models\EmailCampaign;

class EmailCampaignSent
{
    /** @var \Spatie\EmailCampaigns\Models\EmailCampaign */
    public $emailCampaign;

    public function __construct(EmailCampaign $emailCampaign)
    {
        $this->emailCampaign = $emailCampaign;
    }
}

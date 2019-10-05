<?php


namespace Spatie\EmailCampaigns\Events;


use Spatie\EmailCampaigns\Models\Campaign;

class CampaignStatisticsCalculated
{
    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    public $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }
}

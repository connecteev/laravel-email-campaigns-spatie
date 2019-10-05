<?php

namespace Spatie\EmailCampaigns\Events;

use Spatie\EmailCampaigns\Models\Subscription;

class Subscribed
{
    /** @var \Spatie\EmailCampaigns\Models\Subscription */
    public $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }
}

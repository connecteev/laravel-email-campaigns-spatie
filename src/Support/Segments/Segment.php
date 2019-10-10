<?php

namespace Spatie\EmailCampaigns\Support\Segments;

use Illuminate\Database\Eloquent\Builder;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\Subscription;

abstract class Segment
{
    public function getSubscriptionsQuery(Campaign $campaign): Builder
    {
        return $campaign->emailList->subscriptions()->getQuery();
    }

    public function shouldSend(Subscription $subscription, Campaign $campaign): bool
    {
        return true;
    }
}

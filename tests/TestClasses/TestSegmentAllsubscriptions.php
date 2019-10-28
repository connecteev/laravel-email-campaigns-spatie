<?php

namespace Spatie\EmailCampaigns\Tests\TestClasses;

use Illuminate\Database\Eloquent\Builder;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Support\Segments\Segment;

class TestSegmentAllsubscriptions extends Segment
{
    public function getSubscriptionsQuery(Campaign $campaign): Builder
    {
        return Subscription::query();
    }
}

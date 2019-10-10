<?php

namespace Spatie\EmailCampaigns\Tests\TestClasses;

use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Support\Segments\Segment;

class TestSegmentOnlyShouldSendToJohn extends Segment
{
    public function shouldSend(Subscription $subscription, Campaign $campaign): bool
    {
        return $subscription->subscriber->email === 'john@example.com';
    }
}

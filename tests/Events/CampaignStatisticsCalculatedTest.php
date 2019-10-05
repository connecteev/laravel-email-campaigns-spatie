<?php

namespace Spatie\EmailCampaigns\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Jobs\CalculateStatisticsJob;
use Spatie\EmailCampaigns\Events\CampaignStatisticsCalculated;

class CampaignStatisticsCalculatedTest extends TestCase
{
    /** @test */
    public function it_fires_an_event_after_campaign_statistics_have_been_calculated()
    {
        Event::fake(CampaignStatisticsCalculated::class);

        $campaign = factory(Campaign::class)->create();

        dispatch(new CalculateStatisticsJob($campaign));

        Event::assertDispatched(CampaignStatisticsCalculated::class, function (CampaignStatisticsCalculated $event) use ($campaign) {
            $this->assertEquals($campaign->id, $event->campaign->id);

            return true;
        });
    }
}

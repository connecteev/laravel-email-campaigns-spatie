<?php

namespace Spatie\EmailCampaigns\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Events\CampaignSent;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Tests\Factories\CampaignFactory;

class CampaignSentTest extends TestCase
{
    /** @test */
    public function it_fires_an_event_after_a_campaign_has_been_sent()
    {
        Event::fake(CampaignSent::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(3)->create();

        dispatch(new SendCampaignJob($campaign));

        Event::assertDispatched(CampaignSent::class, function (CampaignSent $event) use ($campaign) {
            $this->assertEquals($campaign->id, $event->campaign->id);

            return true;
        });
    }
}

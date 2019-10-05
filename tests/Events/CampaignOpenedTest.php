<?php


namespace Spatie\EmailCampaigns\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\EmailCampaigns\Events\CampaignOpened;
use Spatie\EmailCampaigns\Http\Controllers\TrackOpensController;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Tests\TestCase;

class CampaignOpenedTest extends TestCase
{
    /** @test */
    public function it_fires_an_event_when_a_campaign_is_opened()
    {
        Event::fake(CampaignOpened::class);

        $campaignSend = factory(CampaignSend::class)->create();

        $trackCampaignOpenUrl = action(TrackOpensController::class, $campaignSend->uuid);

        $this
            ->get($trackCampaignOpenUrl)
            ->assertSuccessful();

        Event::assertDispatched(CampaignOpened::class, function(CampaignOpened $event) use ($campaignSend) {
            $this->assertEquals($campaignSend->subscription->subscriber->uuid, $event->campaignOpen->subscriber->uuid);
            $this->assertEquals($campaignSend->campaign->uuid, $event->campaignOpen->campaign->uuid);

            return true;
        });
    }
}

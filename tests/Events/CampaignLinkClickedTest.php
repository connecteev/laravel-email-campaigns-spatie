<?php

namespace Spatie\EmailCampaigns\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Events\CampaignLinkClicked;
use Spatie\EmailCampaigns\Http\Controllers\TrackClicksController;

class CampaignLinkClickedTest extends TestCase
{
    /** @test */
    public function it_will_fire_an_event_when_a_link_gets_clicked()
    {
        Event::fake(CampaignLinkClicked::class);

        $campaignLink = factory(CampaignLink::class)->create();

        $subscriber = factory(Subscriber::class)->create();

        $trackClickUrl = action(TrackClicksController::class, [
            $campaignLink->uuid,
            $subscriber->uuid,
        ]);

        $this
            ->get($trackClickUrl)
            ->assertRedirect();

        Event::assertDispatched(CampaignLinkClicked::class, function (CampaignLinkClicked $event) use ($subscriber, $campaignLink) {
            $campaignClick = $event->campaignClick;
            $this->assertEquals($campaignLink->uuid, $campaignClick->link->uuid);
            $this->assertEquals($subscriber->uuid, $campaignClick->subscriber->uuid);

            return true;
        });
    }
}

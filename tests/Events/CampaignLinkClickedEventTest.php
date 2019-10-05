<?php


namespace Spatie\EmailCampaigns\Tests\Events;


use Illuminate\Support\Facades\Event;
use Spatie\EmailCampaigns\Events\CampaignLinkClicked;
use Spatie\EmailCampaigns\Http\Controllers\TrackClicksController;
use Spatie\EmailCampaigns\Http\Controllers\TrackOpensController;
use Spatie\EmailCampaigns\Models\CampaignClick;
use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Tests\TestCase;

class CampaignLinkClickedEventTest extends TestCase
{
    /** @test */
    public function it_will_fire_an_event_when_a_link_gets_clicked()
    {
        Event::fake();

        $campaignLink = factory(CampaignLink::class)->create();

        $subscriber = factory(Subscriber::class)->create();

        $trackClickUrl = action(TrackClicksController::class, [
            $campaignLink->uuid,
            $subscriber->uuid,
        ]);

        $this
            ->get($trackClickUrl)
            ->assertRedirect();

        Event::assertDispatched(CampaignLinkClicked::class, function(CampaignLinkClicked $event) use ($subscriber, $campaignLink) {
            $campaignClick = $event->campaignClick;
            $this->assertEquals($campaignLink->id, $campaignClick->link->id);
            $this->assertEquals($subscriber->id, $campaignClick->subscriber->id);

            return true;
        });
    }
}

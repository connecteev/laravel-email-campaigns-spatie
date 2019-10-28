<?php

namespace Spatie\EmailCampaigns\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Jobs\SendMailJob;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Events\CampaignMailSent;

class CampaignMailSentTest extends TestCase
{
    /** @test */
    public function it_will_fire_an_event_when_the_mail_is_sent()
    {
        Event::fake(CampaignMailSent::class);

        $send = factory(CampaignSend::class)->create();

        dispatch(new SendMailJob($send));

        Event::assertDispatched(CampaignMailSent::class, function (CampaignMailSent $event) use ($send) {
            $this->assertEquals($send->uuid, $event->campaignSend->uuid);

            return true;
        });
    }
}

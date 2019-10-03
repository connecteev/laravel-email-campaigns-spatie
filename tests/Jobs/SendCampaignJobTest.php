<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Enums\CampaignStatus;
use Spatie\EmailCampaigns\Events\EmailCampaignSent;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Mails\CampaignMail;
use Spatie\EmailCampaigns\Tests\Factories\CampaignFactory;
use Spatie\EmailCampaigns\Tests\TestCase;

class SendCampaignJobTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Mails\CampaignMail $campaign */
    private $campaign;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new CampaignFactory())
            ->withSubscriberCount(3)
            ->create();

        Mail::fake();

        Event::fake();
    }

    /** @test */
    public function it_can_send_a_campaign()
    {
        dispatch(new SendCampaignJob($this->campaign));

        Mail::assertSent(CampaignMail::class, 3);

        Event::assertDispatched(EmailCampaignSent::class, function(EmailCampaignSent $event) {
            $this->assertEquals($this->campaign->id, $event->campaign->id);

            return true;
        });

        $this->campaign->refresh();
        $this->assertEquals(CampaignStatus::SENT, $this->campaign->status);
        $this->assertEquals(3, $this->campaign->sent_to_number_of_subscribers);
    }

    /** @test */
    public function a_campaign_that_was_sent_will_not_be_sent_again()
    {
        $this->assertFalse($this->campaign->wasAlreadySent());
        dispatch(new SendCampaignJob($this->campaign));
        $this->assertTrue($this->campaign->refresh()->wasAlreadySent());
        Mail::assertSent(CampaignMail::class, 3);

        dispatch(new SendCampaignJob($this->campaign));
        Mail::assertSent(CampaignMail::class, 3);
        Event::assertDispatched(EmailCampaignSent::class, 1);
    }
}


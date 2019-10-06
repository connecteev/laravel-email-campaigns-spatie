<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Jobs\RegisterOpenJob;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Mails\CampaignMail;
use Spatie\EmailCampaigns\Events\CampaignSent;
use Spatie\EmailCampaigns\Enums\CampaignStatus;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Tests\Factories\CampaignFactory;
use Spatie\EmailCampaigns\Exceptions\CampaignCouldNotBeSent;

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

        Event::assertDispatched(CampaignSent::class, function (CampaignSent $event) {
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
        Event::assertDispatched(CampaignSent::class, 1);
    }

    /** @test */
    public function it_will_not_send_invalid_html()
    {
        $this->campaign->update([
            'track_clicks' => true,
            'html' => '<qsdfqlsmdkjm><<>><<',
        ]);

        $this->expectException(CampaignCouldNotBeSent::class);

        dispatch(new SendCampaignJob($this->campaign));
    }

    /** @test */
    public function the_queue_of_the_send_campaign_job_can_be_configured()
    {
        Queue::fake();

        config()->set('email-campaigns.perform_on_queue.send_campaign_job', 'custom-queue');

        $campaign = factory(Campaign::class)->create();
        dispatch(new SendCampaignJob($campaign));

        Queue::assertPushedOn('custom-queue', SendCampaignJob::class);
    }
}

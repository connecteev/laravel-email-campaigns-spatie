<?php

namespace Spatie\EmailCampaigns\Tests\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Jobs\SendTestMailJob;
use Spatie\EmailCampaigns\Tests\Factories\CampaignFactory;

class CampaignTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    private $campaign;

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->campaign = Campaign::create()->refresh();
    }

    /** @test */
    public function the_default_status_is_created()
    {
        $this->assertEquals('created', $this->campaign->status);
    }

    /** @test */
    public function it_can_be_marked_to_track_opens()
    {
        $this->assertFalse($this->campaign->track_opens);

        $this->campaign->trackOpens();

        $this->assertTrue($this->campaign->refresh()->track_opens);
    }

    /** @test */
    public function it_can_be_marked_to_track_clicks()
    {
        $this->assertFalse($this->campaign->track_clicks);

        $this->campaign->trackClicks();

        $this->assertTrue($this->campaign->refresh()->track_clicks);
    }

    /** @test */
    public function it_can_add_a_subject()
    {
        $this->assertNull($this->campaign->subject);

        $this->campaign->subject('hello');

        $this->assertEquals('hello', $this->campaign->refresh()->subject);
    }

    /** @test */
    public function it_can_add_a_list()
    {
        $list = factory(EmailList::class)->create();

        $this->campaign->to($list);

        $this->assertEquals($list->id, $this->campaign->refresh()->email_list_id);
    }

    /** @test */
    public function it_can_be_sent()
    {
        $list = factory(EmailList::class)->create();

        $campaign = Campaign::create()
            ->subject('test')
            ->content('my content')
            ->to($list)
            ->send();

        Queue::assertPushed(SendCampaignJob::class, function (SendCampaignJob $job) use ($campaign) {
            $this->assertEquals($campaign->id, $job->campaign->id);

            return true;
        });
    }

    /** @test */
    public function it_has_a_shorthand_to_set_the_list_and_send_it_in_one_go()
    {
        $list = factory(EmailList::class)->create();

        $campaign = Campaign::create()
            ->content('my content')
            ->subject('test')
            ->sendTo($list);

        $this->assertEquals($list->id, $campaign->refresh()->email_list_id);

        Queue::assertPushed(SendCampaignJob::class, function (SendCampaignJob $job) use ($campaign) {
            $this->assertEquals($campaign->id, $job->campaign->id);

            return true;
        });
    }

    /** @test */
    public function it_has_a_scope_that_can_get_campaigns_sent_in_a_certain_period()
    {
        $sentAt1430 = CampaignFactory::createSentAt('2019-01-01 14:30:00');
        $sentAt1530 = CampaignFactory::createSentAt('2019-01-01 15:30:00');
        $sentAt1630 = CampaignFactory::createSentAt('2019-01-01 16:30:00');
        $sentAt1730 = CampaignFactory::createSentAt('2019-01-01 17:30:00');

        $campaigns = Campaign::sentBetween(
            Carbon::createFromFormat('Y-m-d H:i:s', '2019-01-01 13:30:00'),
            Carbon::createFromFormat('Y-m-d H:i:s', '2019-01-01 17:30:00'),
        )->get();

        $this->assertEquals(
            [$sentAt1430->id, $sentAt1530->id, $sentAt1630->id],
            $campaigns->pluck('id')->values()->toArray(),
        );
    }

    /** @test */
    public function it_can_send_out_a_test_email()
    {
        Bus::fake();

        $email = 'john@example.com';

        $this->campaign->sendTestMail($email);

        Bus::assertDispatched(SendTestMailJob::class, function (SendTestMailJob $job) use ($email) {
            $this->assertEquals($this->campaign->id, $job->campaign->id);
            $this->assertEquals($email, $job->email);

            return true;
        });
    }

    /** @test */
    public function it_can_send_out_multiple_test_emails_at_once()
    {
        Bus::fake();

        $this->campaign->sendTestMail(['john@example.com', 'paul@example.com']);

        Bus::assertDispatched(SendTestMailJob::class, function (SendTestMailJob $job) {
            return $job->email === 'john@example.com';
        });

        Bus::assertDispatched(SendTestMailJob::class, function (SendTestMailJob $job) {
            return $job->email === 'paul@example.com';
        });
    }
}

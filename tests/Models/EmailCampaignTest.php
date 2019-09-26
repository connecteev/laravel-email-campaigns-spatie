<?php

namespace Spatie\EmailCampaigns\Tests\Models;

use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Models\EmailCampaign;

class EmailCampaignTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\EmailCampaign */
    private $campaign;

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        $this->campaign = EmailCampaign::create()->refresh();
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

        $campaign = EmailCampaign::create()
            ->subject('test')
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

        $campaign = EmailCampaign::create()
            ->subject('test')
            ->sendTo($list);

        $this->assertEquals($list->id, $campaign->refresh()->email_list_id);

        Queue::assertPushed(SendCampaignJob::class, function (SendCampaignJob $job) use ($campaign) {
            $this->assertEquals($campaign->id, $job->campaign->id);

            return true;
        });
    }
}

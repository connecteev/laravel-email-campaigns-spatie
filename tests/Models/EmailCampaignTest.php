<?php

namespace Spatie\EmailCampaigns\Tests\Models;

use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Tests\TestCase;

class EmailCampaignTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\EmailCampaign */
    private $campaign;

    public function setUp(): void
    {
        parent::setUp();

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
}


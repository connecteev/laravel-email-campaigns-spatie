<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Mails\CampaignMail;
use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Tests\Factories\EmailCampaignFactory;
use Spatie\EmailCampaigns\Tests\TestCase;

class SendCampaignJobTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Mails\CampaignMail $campaign */
    private $campaign;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new EmailCampaignFactory())
            ->withSubscriberCount(3)
            ->create();

        Mail::fake();
    }

    /** @test */
    public function it_can_send_a_campaign()
    {
        dispatch(new SendCampaignJob($this->campaign));

        Mail::assertSent(CampaignMail::class, 3);
    }
}


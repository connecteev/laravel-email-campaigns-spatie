<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Jobs\RegisterClickJob;
use Spatie\EmailCampaigns\Jobs\SendMailJob;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Tests\TestCase;

class RegisterClickJobTest extends TestCase
{
    /** @test */
    public function the_queue_of_the_register_click_job_can_be_configured()
    {
        Queue::fake();

        config()->set('email-campaigns.perform_on_queue.register_click_job', 'custom-queue');

        dispatch(new RegisterClickJob('campaign-link-uuid', 'subscriber-uuid'));

        Queue::assertPushedOn('custom-queue', RegisterClickJob::class);
    }
}

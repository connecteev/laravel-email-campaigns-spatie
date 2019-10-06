<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Jobs\RegisterOpenJob;

class RegisterOpenJobTest extends TestCase
{
    /** @test */
    public function the_queue_of_the_register_open_job_can_be_configured()
    {
        Queue::fake();

        config()->set('email-campaigns.perform_on_queue.register_open_job', 'custom-queue');

        dispatch(new RegisterOpenJob('campaign-send-uuid'));

        Queue::assertPushedOn('custom-queue', RegisterOpenJob::class);
    }
}

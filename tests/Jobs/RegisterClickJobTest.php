<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Jobs\RegisterClickJob;

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

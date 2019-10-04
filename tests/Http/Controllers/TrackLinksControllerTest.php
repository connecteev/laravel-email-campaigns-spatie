<?php

namespace Spatie\EmailCampaigns\Tests\Http\Controllers;

use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Http\Controllers\TrackClicksController;
use Spatie\EmailCampaigns\Jobs\RegisterClickJob;
use Spatie\EmailCampaigns\Tests\TestCase;

class TrackLinksControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /** @test */
    public function it_will_dispatch_a_job_to_register_a_click()
    {
        $this
            ->get(action(TrackClicksController::class, ['campaignLinkUuid', 'subscriberUuid']) . '?redirect=https://mylink.com')
            ->assertRedirect('https://mylink.com');

        Queue::assertPushed(RegisterClickJob::class, function (RegisterClickJob $job) {
            $this->assertEquals('campaignLinkUuid', $job->campaignLinkUuid);
            $this->assertEquals('subscriberUuid', $job->subscriberUuid);

            return true;
        });
    }
}


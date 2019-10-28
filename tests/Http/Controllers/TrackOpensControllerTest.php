<?php

namespace Spatie\EmailCampaigns\Tests\Http\Controllers;

use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Jobs\RegisterOpenJob;
use Spatie\EmailCampaigns\Http\Controllers\TrackOpensController;

class TrackOpensControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /** @test */
    public function it_will_dispatch_a_job_to_register_an_open()
    {
        $this
            ->get(action(TrackOpensController::class, ['campaignSendUuid']))
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'image/gif');

        Queue::assertPushed(RegisterOpenJob::class, function (RegisterOpenJob $job) {
            $this->assertEquals('campaignSendUuid', $job->campaignSendUuid);

            return true;
        });
    }
}

<?php

namespace Spatie\EmailCampaigns\Tests\Commands;

use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Jobs\SendMailJob;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Commands\RetryPendingSendsCommand;

class RetryPendingSendsCommandTest extends TestCase
{
    /** @test */
    public function it_will_dispatch_a_job_for_each_pending_CampaignSend()
    {
        Queue::fake();

        $pendingCampaignSend = factory(CampaignSend::class)->create([
            'sent_at' => null,
        ]);

        $sentCampaignSend = factory(CampaignSend::class)->create([
            'sent_at' => now(),
        ]);

        $this->artisan(RetryPendingSendsCommand::class)->assertExitCode(0);

        Queue::assertPushed(SendMailJob::class, 1);
        Queue::assertPushed(SendMailJob::class, function (SendMailJob $job) use ($pendingCampaignSend) {
            return $job->pendingSend->id === $pendingCampaignSend->id;
        });
    }
}

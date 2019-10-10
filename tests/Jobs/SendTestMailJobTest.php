<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Mails\CampaignMailable;
use Spatie\EmailCampaigns\Jobs\SendTestMailJob;

class SendTestMailJobTest extends TestCase
{
    /** @test */
    public function it_can_send_a_test_email()
    {
        Mail::fake();

        $campaign = factory(Campaign::class)->create([
            'html' => 'my html',
        ]);

        $email = 'john@example.com';

        dispatch(new SendTestMailJob($campaign, $email));

        Mail::assertSent(CampaignMailable::class, function (CampaignMailable $mail) use ($email, $campaign) {
            $this->assertEquals($campaign->subject, $mail->subject);

            $this->assertTrue($mail->hasTo($email));

            return true;
        });
    }

    /** @test */
    public function the_queue_of_the_send_test_mail_job_can_be_configured()
    {
        Queue::fake();
        config()->set('email-campaigns.perform_on_queue.send_test_mail_job', 'custom-queue');

        $campaign = factory(Campaign::class)->create();
        dispatch(new SendTestMailJob($campaign, 'john@example.com'));
        Queue::assertPushedOn('custom-queue', SendTestMailJob::class);
    }
}

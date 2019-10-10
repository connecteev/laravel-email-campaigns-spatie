<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Jobs\SendMailJob;
use Spatie\EmailCampaigns\Mails\CampaignMail;
use Spatie\EmailCampaigns\Models\CampaignSend;

class SendMailJobTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    /** @test */
    public function it_can_send_a_mail()
    {
        $pendingSend = factory(CampaignSend::class)->create();

        dispatch(new SendMailJob($pendingSend));

        Mail::assertSent(CampaignMail::class, function (CampaignMail $mail) use ($pendingSend) {
            $this->assertEquals($pendingSend->campaign->subject, $mail->subject);

            $this->assertTrue($mail->hasTo($pendingSend->subscription->subscriber->email));

            return true;
        });
    }

    /** @test */
    public function it_will_not_resend_a_mail_that_has_already_been_sent()
    {
        $pendingSend = factory(CampaignSend::class)->create();

        $this->assertFalse($pendingSend->wasAlreadySent());

        dispatch(new SendMailJob($pendingSend));

        $this->assertTrue($pendingSend->refresh()->wasAlreadySent());
        Mail::assertSent(CampaignMail::class, 1);

        dispatch(new SendMailJob($pendingSend));
        Mail::assertSent(CampaignMail::class, 1);
    }

    /** @test */
    public function the_queue_of_the_send_mail_job_can_be_configured()
    {
        Queue::fake();
        config()->set('email-campaigns.perform_on_queue.send_mail_job', 'custom-queue');

        $pendingSend = factory(CampaignSend::class)->create();
        dispatch(new SendMailJob($pendingSend));
        Queue::assertPushedOn('custom-queue', SendMailJob::class);
    }
}

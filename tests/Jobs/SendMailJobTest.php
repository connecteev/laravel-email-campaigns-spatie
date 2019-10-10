<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Jobs\SendMailJob;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Mails\CampaignMailable;
use Spatie\EmailCampaigns\Tests\TestClasses\TestCampaignMailable;

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

        Mail::assertSent(CampaignMailable::class, function (CampaignMailable $mail) use ($pendingSend) {
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
        Mail::assertSent(CampaignMailable::class, 1);

        dispatch(new SendMailJob($pendingSend));
        Mail::assertSent(CampaignMailable::class, 1);
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

    /** @test */
    public function it_can_use_a_custom_mailable()
    {
        $pendingSend = factory(CampaignSend::class)->create();

        $pendingSend->campaign->useMailable(TestCampaignMailable::class);

        dispatch(new SendMailJob($pendingSend));

        Mail::assertSent(TestCampaignMailable::class, 1);
    }
}

<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Jobs\SendMailJob;
use Spatie\EmailCampaigns\Mails\CampaignMail;
use Spatie\EmailCampaigns\Models\EmailCampaignSend;

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
        $pendingSend = factory(EmailCampaignSend::class)->create();

        dispatch(new SendMailJob($pendingSend));

        Mail::assertSent(CampaignMail::class, function (CampaignMail $mail) use ($pendingSend) {
            $this->assertEquals($pendingSend->emailCampaign->subject, $mail->subject);

            $this->assertTrue($mail->hasTo($pendingSend->emailListSubscription->subscriber->email));

            return true;
        });
    }

    /** @test */
    public function it_will_not_resend_a_mail_that_has_already_been_sent()
    {
        $pendingSend = factory(EmailCampaignSend::class)->create();

        $this->assertFalse($pendingSend->wasAlreadySent());

        dispatch(new SendMailJob($pendingSend));

        $this->assertTrue($pendingSend->refresh()->wasAlreadySent());
        Mail::assertSent(CampaignMail::class, 1);

        dispatch(new SendMailJob($pendingSend));
        Mail::assertSent(CampaignMail::class, 1);
    }
}

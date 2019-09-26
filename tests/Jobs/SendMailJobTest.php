<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Jobs\SendMailJob;
use Spatie\EmailCampaigns\Mails\CampaignMail;
use Spatie\EmailCampaigns\Models\EmailCampaignSend;
use Spatie\EmailCampaigns\Tests\TestCase;

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

            $this->assertTrue($mail->hasTo($pendingSend->emailListSubscriber->email));

            return true;
        });
    }
}

<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Jobs\SendTestMailJob;
use Spatie\EmailCampaigns\Mails\CampaignMail;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Tests\TestCase;

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

        Mail::assertSent(CampaignMail::class, function (CampaignMail $mail) use ($email, $campaign) {
            $this->assertEquals($campaign->subject, $mail->subject);

            $this->assertTrue($mail->hasTo($email));

            return true;
        });
    }
}

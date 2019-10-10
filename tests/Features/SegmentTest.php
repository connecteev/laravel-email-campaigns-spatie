<?php

namespace Spatie\EmailCampaigns\Tests\Features;

use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Mails\CampaignMailable;
use Spatie\EmailCampaigns\Tests\TestClasses\TestSegmentQueryOnlyJohn;
use Spatie\EmailCampaigns\Tests\TestClasses\TestSegmentAllsubscriptions;
use Spatie\EmailCampaigns\Tests\TestClasses\TestSegmentOnlyShouldSendToJohn;

class SegmentTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    private $campaign;

    /** @var \Spatie\EmailCampaigns\Models\EmailList */
    private $emailList;

    public function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->campaign = factory(Campaign::class)->create();

        $this->emailList = factory(EmailList::class)->create();
    }

    /** @test */
    public function it_can_segment_a_test_by_using_should_send()
    {
        $this->campaign->useSegment(TestSegmentOnlyShouldSendToJohn::class);

        $this->emailList->subscribe('john@example.com');
        $this->emailList->subscribe('jane@example.com');

        $this->campaign
            ->useSegment(TestSegmentOnlyShouldSendToJohn::class)
            ->sendTo($this->emailList);

        Mail::assertSent(CampaignMailable::class, 1);

        Mail::assertSent(CampaignMailable::class, function (CampaignMailable $mail) {
            return $mail->hasTo('john@example.com');
        });

        Mail::assertNotSent(CampaignMailable::class, function (CampaignMailable $mail) {
            return $mail->hasTo('jane@example.com');
        });
    }

    /** @test */
    public function it_can_segment_a_test_by_using_a_query()
    {
        $this->emailList->subscribe('john@example.com');
        $this->emailList->subscribe('jane@example.com');

        $this->campaign
            ->useSegment(TestSegmentQueryOnlyJohn::class)
            ->sendTo($this->emailList);

        Mail::assertSent(CampaignMailable::class, 1);

        Mail::assertSent(CampaignMailable::class, function (CampaignMailable $mail) {
            return $mail->hasTo('john@example.com');
        });

        Mail::assertNotSent(CampaignMailable::class, function (CampaignMailable $mail) {
            return $mail->hasTo('jane@example.com');
        });
    }

    /** @test */
    public function it_will_not_send_a_mail_if_it_is_not_subscribed_to_the_list_of_the_campaign_even_if_the_segment_selects_it()
    {
        factory(Subscription::class)->create();

        $this->campaign->useSegment(TestSegmentAllsubscriptions::class)->sendTo($this->emailList);

        Mail::assertNothingSent();
    }
}

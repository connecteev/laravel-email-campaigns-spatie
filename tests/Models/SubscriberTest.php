<?php

namespace Spatie\EmailCampaigns\Tests\Models;

use Illuminate\Support\Facades\Mail;
use PharIo\Manifest\Email;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Mails\ConfirmSubscriptionMail;

class SubscriberTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\Subscriber */
    private $subscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->subscriber = factory(Subscriber::class)->create();

        Mail::fake();
    }

    /** @test */
    public function it_can_subscribe_itself_to_a_list()
    {
        $list = factory(EmailList::class)->create();

        $this->assertFalse($this->subscriber->isSubscribedTo($list));
        $subscription = $this->subscriber->subscribeTo($list);
        $this->assertTrue($this->subscriber->isSubscribedTo($list));

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscription->status);
    }

    public function it_will_only_subscribe_a_subscriber_once()
    {
        $list = factory(EmailList::class)->create();

        $this->assertFalse($this->subscriber->isSubscribedTo($list));
        $this->subscriber->subscribeTo($list);
        $this->subscriber->subscribeTo($list);

        $this->assertEquals(1, Subscription::count());
    }

    /** @test */
    public function it_can_resubscribe_someone()
    {
        $list = factory(EmailList::class)->create();

        $subscription = $this->subscriber->subscribeTo($list);
        $this->assertTrue($this->subscriber->isSubscribedTo($list));

        $subscription->markAsUnsubscribed();
        $this->assertFalse($this->subscriber->isSubscribedTo($list));

        $this->subscriber->subscribeTo($list);
        $this->assertTrue($this->subscriber->isSubscribedTo($list));
    }

    /** @test */
    public function it_will_send_a_confirmation_mail_if_the_list_requires_double_optin()
    {
        $list = factory(EmailList::class)->create([
            'requires_double_opt_in' => true,
        ]);

        $subscription = $this->subscriber->subscribeTo($list);

        $this->assertFalse($this->subscriber->isSubscribedTo($list));

        Mail::assertQueued(ConfirmSubscriptionMail::class, function (ConfirmSubscriptionMail $mail) use ($subscription) {
            $this->assertEquals($subscription->id, $mail->subscription->id);

            return true;
        });
    }

    /** @test */
    public function it_can_immediately_subscribe_someone_and_not_send_a_mail_even_with_double_opt_in_enabled()
    {
        $list = factory(EmailList::class)->create([
            'requires_double_opt_in' => true,
        ]);

        $this->subscriber->subscribeNowTo($list);

        $this->assertTrue($this->subscriber->isSubscribedTo($list));

        Mail::assertNotQueued(ConfirmSubscriptionMail::class);
    }

    /** @test */
    public function no_email_will_be_sent_when_adding_someone_that_was_already_subscribed()
    {
        $subscription = factory(Subscription::class)->create();

        $subscription->emailList->update(['requires_double_opt_in' => true]);

        $subscription->subscriber->subscribeTo($subscription->emailList);

        Mail::assertNothingQueued();
    }

    /** @test */
    public function it_can_get_all_email_lists_subscribed_to()
    {
        $emailList = factory(EmailList::class)->create();

        $this->subscriber->subscribeNowTo($emailList);

        $this->assertCount(1, $this->subscriber->refresh()->emailLists);

        $this->subscriber->unsubscribeFrom($emailList);

        $this->assertCount(0, $this->subscriber->refresh()->emailLists);

    }
}

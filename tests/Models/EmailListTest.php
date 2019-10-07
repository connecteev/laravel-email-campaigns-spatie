<?php

namespace Spatie\EmailCampaigns\Tests\Models;

use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Models\Subscription;

class EmailListTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\EmailList */
    private $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = factory(EmailList::class)->create();
    }

    /** @test */
    public function it_can_add_a_subscriber_to_a_list()
    {
        $subscription = $this->emailList->subscribe('john@example.com');

        $this->assertEquals('john@example.com', $subscription->subscriber->email);
    }

    /** @test */
    public function when_adding_someone_that_was_already_subscribed_no_new_subscription_will_be_created()
    {
        $this->emailList->subscribe('john@example.com');
        $this->emailList->subscribe('john@example.com');

        $this->assertEquals(1, Subscription::count());
        $this->assertEquals(1, Subscriber::count());
    }

    /** @test */
    public function it_can_unsubscribe_someone()
    {
        $this->emailList->subscribe('john@example.com');

        $this->assertTrue($this->emailList->unsubscribe('john@example.com'));
        $this->assertFalse($this->emailList->unsubscribe('non-existing-subscriber@example.com'));

        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, Subscription::first()->status);
    }

    /** @test */
    public function it_can_get_all_subscribers_that_are_subscribed()
    {
        $this->emailList->subscribe('john@example.com');
        $this->emailList->subscribe('jane@example.com');
        $this->emailList->unsubscribe('john@example.com');

        $subscribers = $this->emailList->subscribers;
        $this->assertCount(1, $subscribers);
        $this->assertEquals('jane@example.com', $subscribers->first()->email);

        $subscribers = $this->emailList->allSubscribers;
        $this->assertCount(2, $subscribers);
    }
}

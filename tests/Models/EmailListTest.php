<?php

namespace Spatie\EmailCampaigns\Tests\Models;

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
}

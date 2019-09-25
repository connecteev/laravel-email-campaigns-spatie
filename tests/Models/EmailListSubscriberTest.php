<?php

namespace Spatie\EmailCampaigns\Tests\Models;

use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;
use Spatie\EmailCampaigns\Models\EmailListSubscription;
use Spatie\EmailCampaigns\Tests\TestCase;

class EmailListSubscriberTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\EmailListSubscriber  */
    private $subscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->subscriber = factory(EmailListSubscriber::class)->create();
    }

    /** @test */
    public function it_can_subscribe_itself_to_a_list()
    {
        $list = factory(EmailList::class)->create();

        $this->assertFalse($this->subscriber->isSubscribedTo($list));
        $this->subscriber->subscribeTo($list);
        $this->assertTrue($this->subscriber->isSubscribedTo($list));
    }

    public function it_will_only_subscribe_a_subscriber_once()
    {
        $list = factory(EmailList::class)->create();

        $this->assertFalse($this->subscriber->isSubscribedTo($list));
        $this->subscriber->subscribeTo($list);
        $this->subscriber->subscribeTo($list);

        $this->assertEquals(1, EmailListSubscription::count());
    }
}


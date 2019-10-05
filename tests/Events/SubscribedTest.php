<?php

namespace Spatie\EmailCampaigns\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\EmailCampaigns\Events\Subscribed;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Tests\TestCase;

class SubscribedTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake(Subscribed::class);
    }

    /** @test */
    public function it_send_out_an_event_when_someone_subscribes()
    {
        /** @var \Spatie\EmailCampaigns\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create([
            'requires_double_opt_in' => false,
        ]);

        $emailList->subscribe('john@example.com');

        Event::assertDispatched(Subscribed::class, function(Subscribed $event) {
            $this->assertEquals('john@example.com', $event->subscription->subscriber->email);

            return true;
        });
    }

    /** @test */
    public function it_will_not_fire_the_subscription_event_when_a_subscription_still_needs_to_be_confirmed()
    {
        /** @var \Spatie\EmailCampaigns\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create([
            'requires_double_opt_in' => true,
        ]);

        $emailList->subscribe('john@example.com');

        Event::assertNotDispatched(Subscribed::class);
    }

    /** @test */
    public function it_will_fire_the_subscribe_event_when_a_subscription_is_confirmed()
    {
        /** @var \Spatie\EmailCampaigns\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create([
            'requires_double_opt_in' => true,
        ]);

        $subscription = $emailList->subscribe('john@example.com');

        Event::assertNotDispatched(Subscribed::class);

        $subscription->confirm();

        Event::assertDispatched(Subscribed::class, function(Subscribed $event) {
            $this->assertEquals('john@example.com', $event->subscription->subscriber->email);

            return true;
        });
    }
}

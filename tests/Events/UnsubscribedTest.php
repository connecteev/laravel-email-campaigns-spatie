<?php


namespace Spatie\EmailCampaigns\Tests\Events;

use Illuminate\Support\Facades\Event;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Events\Unsubscribed;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Tests\TestCase;

class UnsubscribedTest extends TestCase
{
    /** @test */
    public function it_will_fire_an_event_when_someone_unsubscribes()
    {
        Event::fake(Unsubscribed::class);

        /** @var \Spatie\EmailCampaigns\Models\Subscription $subscription */
        $subscription = factory(Subscription::class)->create([
           'status' => SubscriptionStatus::SUBSCRIBED,
        ]);

        $subscription->markAsUnsubscribed();

        Event::assertDispatched(Unsubscribed::class, function(Unsubscribed $event) use ($subscription) {
            $this->assertEquals($subscription->id, $event->subscription->id);

            return true;
        });
    }
}

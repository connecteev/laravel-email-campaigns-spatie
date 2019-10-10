<?php

namespace Spatie\EmailCampaigns\Tests\Features;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;
use Spatie\EmailCampaigns\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Tests\Factories\CampaignFactory;

class UnsubscribeTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    private $campaign;

    /** @var string */
    private $mailedUnsubscribeLink;

    /** @var \Spatie\EmailCampaigns\Models\EmailList */
    private $emailList;

    /** @var \Spatie\EmailCampaigns\Models\Subscriber */
    private $subscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'html' => '<a href="::unsubscribeUrl::">Unsubscribe</a>',
        ]);

        $this->emailList = $this->campaign->emailList;

        $this->subscriber = $this->campaign->emailList->subscribers->first();
    }

    /** @test */
    public function it_can_unsubscribe_from_a_list()
    {
        $this->sendCampaign();

        $this->assertTrue($this->subscriber->isSubscribedTo($this->emailList));

        $content = $this
            ->get($this->mailedUnsubscribeLink)
            ->assertSuccessful()
            ->baseResponse->content();

        $this->assertStringContainsString('unsubscribed', $content);

        $this->assertFalse($this->subscriber->isSubscribedTo($this->emailList));

        $subscription = $this->emailList->allSubscriptions->first();
        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscription->status);
    }

    protected function sendCampaign()
    {
        Event::listen(MessageSent::class, function (MessageSent $event) {
            $link = (new Crawler($event->message->getBody()))
                ->filter('a')->first()->attr('href');

            $this->assertStringStartsWith('http://localhost', $link);

            $this->mailedUnsubscribeLink = Str::after($link, 'http://localhost');
        });

        dispatch(new SendCampaignJob($this->campaign));
    }

    /** @test */
    public function the_unsubscribe_header_is_added_to_the_email()
    {
        Event::listen(MessageSent::class, function (MessageSent $event) {

            $subscription = $this->emailList->allSubscriptions->first();

            $this->assertNotNull($event->message->getHeaders()->get('List-Unsubscribe'));

            $this->assertNotNull($event->message->getHeaders()->get('List-Unsubscribe'));

            $this->assertEquals('List-Unsubscribe=One-Click' ,$event->message->getHeaders()->get('List-Unsubscribe'));

            $this->assertEquals('<'. url('/unsubscribe/'. $subscription->uuid) .'>', $event->message->getHeaders()->get('List-Unsubscribe'));
        });

        dispatch(new SendCampaignJob($this->campaign));
    }
}

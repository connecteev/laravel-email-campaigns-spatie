<?php

namespace Spatie\EmailCampaigns\Tests\Features;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;
use Spatie\EmailCampaigns\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Models\CampaignUnsubscribe;
use Spatie\EmailCampaigns\Tests\Factories\CampaignFactory;
use Spatie\EmailCampaigns\Http\Controllers\UnsubscribeController;

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

        $this->assertCount(1, CampaignUnsubscribe::all());
        $campaignUnsubscribe = CampaignUnsubscribe::first();

        $this->assertEquals($this->subscriber->uuid, $campaignUnsubscribe->subscriber->uuid);
        $this->assertEquals($this->campaign->uuid, $campaignUnsubscribe->campaign->uuid);

        $subscription = $this->emailList->allSubscriptions->first();
        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscription->status);
    }

    /** @test */
    public function it_will_only_store_a_single_unsubscribe_even_if_the_unsubscribe_link_is_used_multiple_times()
    {
        $this->sendCampaign();

        $this->get($this->mailedUnsubscribeLink)->assertSuccessful();
        $response = $this->get($this->mailedUnsubscribeLink)->assertSuccessful()->baseResponse->content();

        $this->assertCount(1, CampaignUnsubscribe::all());

        $this->assertStringContainsString('already unsubscribed', $response);
    }

    /** @test */
    public function the_unsubscribe_will_work_even_if_the_campaign_send_is_deleted()
    {
        $this->sendCampaign();

        CampaignSend::truncate();

        $this->get($this->mailedUnsubscribeLink)->assertSuccessful();

        $this->assertFalse($this->subscriber->isSubscribedTo($this->emailList));
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

            $this->assertEquals('<'.url(action(UnsubscribeController::class, $subscription->uuid)).'>', $event->message->getHeaders()->get('List-Unsubscribe')->getValue());

            $this->assertNotNull($event->message->getHeaders()->get('List-Unsubscribe-Post'));

            $this->assertEquals('List-Unsubscribe=One-Click', $event->message->getHeaders()->get('List-Unsubscribe-Post')->getValue());
        });

        dispatch(new SendCampaignJob($this->campaign));
    }
}

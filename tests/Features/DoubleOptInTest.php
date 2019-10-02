<?php

namespace Spatie\EmailCampaigns\Tests\Features;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\EmailCampaigns\Http\Controllers\ConfirmSubscriptionController;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class DoubleOptInTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\EmailList */
    private $emailList;

    /** @var string */
    private $mailedLink;

    public function setUp(): void
    {
        parent::setUp();

        /** @var \Spatie\EmailCampaigns\Models\EmailList $emailList */
        $this->emailList = factory(EmailList::class)->create(['requires_double_opt_in' => true]);

        Event::listen(MessageSent::class, function (MessageSent $event) {
            $link = (new Crawler($event->message->getBody()))
                ->filter('.button-primary')->first()->attr('href');

            $this->mailedLink = Str::after($link, 'http://localhost');
        });

        $this->emailList->subscribe('john@example.com');
    }

    /** @test */
    public function when_subscribing_to_a_double_opt_in_list_a_click_in_the_confirmation_mail_is_needed_to_subscribe() {
        $this->assertFalse($this->emailList->isSubscribed('john@example.com'));

        $this
            ->get($this->mailedLink)
            ->assertSuccessful();

        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));
    }

    /** @test */
    public function clicking_the_mailed_link_twice_will_not_result_in_a_double_subscription()
    {
        $this
            ->get($this->mailedLink)
            ->assertSuccessful();

        $content = $this
            ->get($this->mailedLink)
            ->assertSuccessful()
            ->baseResponse->content();

        $this->assertStringContainsString('already confirmed', $content);

        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));
        $this->assertEquals(1, Subscription::count());
    }

    /** @test */
    public function clicking_on_an_invalid_link_will_render_to_correct_response()
    {
        $content = $this
            ->get(action(ConfirmSubscriptionController::class, 'invalid-uuid'))
            ->assertSuccessful()
            ->baseResponse->content();

        $this->assertStringContainsString("The link you clicked seems invalid", $content);
    }
}


<?php

namespace Spatie\EmailCampaigns\Tests\Features;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class DoubleOptinTest extends TestCase
{
    /** @var string */
    private $mailedLink;

    public function setUp(): void
    {
        parent::setUp();

        Event::listen(MessageSent::class, function (MessageSent $event) {

            $link = (new Crawler($event->message->getBody()))
                ->filter('.button-primary')->first()->attr('href');

            $this->mailedLink = Str::after($link, 'http://localhost');
        });
    }

    /** @test */
    public function when_subscribing_to_a_double_opt_in_list_a_click_in_the_confirmation_mail_is_needed_to_subscribe() {
        /** @var \Spatie\EmailCampaigns\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create(['requires_double_opt_in' => true]);

        $emailList->subscribe('john@example.com');

        $this->assertFalse($emailList->isSubscribed('john@example.com'));

        $content = $this
            ->get($this->mailedLink)
            ->assertSuccessful();

        $this->assertTrue($emailList->isSubscribed('john@example.com'));
    }


}


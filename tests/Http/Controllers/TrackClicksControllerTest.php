<?php

namespace Spatie\EmailCampaigns\Tests\Http\Controllers;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Tests\Factories\EmailCampaignFactory;
use Spatie\EmailCampaigns\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class TrackClicksControllerTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\EmailCampaign */
    private $campaign;

    /** @var string */
    private $link;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new EmailCampaignFactory())->withSubscriberCount(1)->create([
            'track_clicks' => true,
            'html' => 'my link: <a href="https://spatie.be">Spatie</a>',
        ]);

        Event::listen(MessageSent::class, function(MessageSent $event) {
            $link = (new Crawler($event->message->getBody()))
            ->filter('a')->first()->attr('href');

            return $this->link = Str::after($link, 'http://localhost');
        });

        dispatch(new SendCampaignJob($this->campaign));
    }

    /** @test */
    public function it_tests()
    {
        $this->assertTrue(true);
    }
}


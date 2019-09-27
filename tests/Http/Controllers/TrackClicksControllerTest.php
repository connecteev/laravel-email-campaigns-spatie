<?php

namespace Spatie\EmailCampaigns\Tests\Http\Controllers;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Tests\Factories\EmailCampaignFactory;
use Spatie\EmailCampaigns\Tests\TestCase;

class TrackClicksControllerTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\EmailCampaign */
    private $campaign;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new EmailCampaignFactory())->withSubscriberCount(1)->create([
            'track_clicks' => true,
            'html' => 'my link: <a href="https://spatie.be">Spatie</a>',
        ]);

        Event::listen(MessageSent::class, function(MessageSent $event) {
            dd($event->message->getBody());
        });

        dispatch(new SendCampaignJob($this->campaign));



    }

    /** @test */
    public function it_tests()
    {

    }
}


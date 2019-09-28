<?php

namespace Spatie\EmailCampaigns\Tests\Features;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Models\CampaignClick;
use Spatie\EmailCampaigns\Tests\Factories\EmailCampaignFactory;
use Spatie\EmailCampaigns\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class TrackLinksTest extends TestCase
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

        Event::listen(MessageSent::class, function (MessageSent $event) {
            $link = (new Crawler($event->message->getBody()))
                ->filter('a')->first()->attr('href');

            return $this->link = Str::after($link, 'http://localhost');
        });

        dispatch(new SendCampaignJob($this->campaign));
    }

    /** @test */
    public function it_can_register_a_click()
    {
        $this
            ->get($this->link)
            ->assertRedirect('https://spatie.be');

        $this->assertDatabaseHas('campaign_clicks', [
            'campaign_link_id' => $this->campaign->links->first()->id,
            'email_subscriber_id' => $this->campaign->emailList->subscribers->first()->id,
        ]);
    }

    /** @test */
    public function it_will_register_multiple_clicks()
    {
        $this->assertEquals(0, CampaignClick::count());

        foreach(range(1,3) as $i) {
            $this
                ->get($this->link)
                ->assertRedirect('https://spatie.be');
        }

        $this->assertEquals(3, CampaignClick::count());
    }
}

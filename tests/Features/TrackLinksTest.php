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
    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    private $campaign;

    /** @var string */
    private $link;

    /** @test */
    public function it_can_register_a_click()
    {
        $this->sendCampaign([
            'track_clicks' => true,
            'html' => 'my link: <a href="https://spatie.be">Spatie</a>',
        ]);

        $this
            ->get($this->link)
            ->assertRedirect('https://spatie.be');

        $this->assertDatabaseHas('campaign_clicks', [
            'campaign_link_id' => $this->campaign->links->first()->id,
            'email_list_subscriber_id' => $this->campaign->emailList->subscribers->first()->id,
        ]);
    }

    /** @test */
    public function it_will_register_multiple_clicks()
    {
        $this->sendCampaign([
            'track_clicks' => true,
            'html' => 'my link: <a href="https://spatie.be">Spatie</a>',
        ]);

        $this->assertEquals(0, CampaignClick::count());

        foreach(range(1,3) as $i) {
            $this
                ->get($this->link)
                ->assertRedirect('https://spatie.be');
        }

        $this->assertEquals(3, CampaignClick::count());
    }

    /** @test */
    public function it_will_not_replace_links_if_clicks_should_not_be_tracked()
    {
        $this->sendCampaign([
            'track_clicks' => false,
            'html' => 'my link: <a href="https://spatie.be">Spatie</a>',
        ]);

        $this->assertEquals('https://spatie.be', $this->link);
    }

    protected function sendCampaign(array $attributes)
    {
        $this->campaign = (new EmailCampaignFactory())->withSubscriberCount(1)->create($attributes);

        Event::listen(MessageSent::class, function (MessageSent $event) {
            $link = (new Crawler($event->message->getBody()))
                ->filter('a')->first()->attr('href');

            $this->link = Str::after($link, 'http://localhost');
        });

        dispatch(new SendCampaignJob($this->campaign));
    }
}

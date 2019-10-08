<?php

namespace Spatie\EmailCampaigns\Tests\Actions;

use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;

class PrepareEmailHtmlActionTest extends TestCase
{
    /** @test */
    public function it_can_make_links_trackable()
    {
        $campaign = factory(Campaign::class)->create([
            'html' => '<a href="https://spatie.be">My website</a>',
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_will_automatically_add_html_tags()
    {
        $myHtml = '<h1>Hello</h1><p>Hello world</p>';

        $campaign = factory(Campaign::class)->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertEquals("<html>{$myHtml}</html>", $campaign->email_html);
    }

    /** @test */
    public function it_will_add_html_tags_if_the_are_already_present()
    {
        $myHtml = '<html><h1>Hello</h1><p>Hello world</p></html>';

        $campaign = factory(Campaign::class)->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertEquals($myHtml, $campaign->email_html);
    }
}

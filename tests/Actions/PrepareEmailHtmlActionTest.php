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
    public function it_correctly_parses_html()
    {
        $campaign = factory(Campaign::class)->create([
            'html' => <<<HTML
                <h1>Hello</h1>
                
                @@unsubscribeUrl@@
            HTML,
        ]);


        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertEquals(
            <<<HTML
                <h1>Hello</h1>@@unsubscribeUrl@@
            HTML,
            $campaign->email_html
        );
    }
}

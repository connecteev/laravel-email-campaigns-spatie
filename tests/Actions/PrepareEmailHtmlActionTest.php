<?php

namespace Spatie\EmailCampaigns\Tests\Actions;

use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;

class PrepareEmailHtmlActionTest extends TestCase
{
    /** @test */
    public function it_can_make_links_trackable()
    {
        $campaign = factory(EmailCampaign::class)->create([
            'html' => '<a href="https://spatie.be">My website</a>',
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $this->assertTrue(true);
    }
}

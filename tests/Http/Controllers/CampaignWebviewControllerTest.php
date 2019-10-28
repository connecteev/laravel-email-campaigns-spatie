<?php

namespace Spatie\EmailCampaigns\Tests\Http\Controllers;

use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Enums\CampaignStatus;
use Spatie\EmailCampaigns\Http\Controllers\CampaignWebviewController;

class CampaignWebviewControllerTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    private $campaign;

    /** @var string */
    private $webviewUrl;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = factory(Campaign::class)->create([
            'webview_html' => 'my webview html',
        ]);

        $this->campaign->markAsSent(1);

        $this->webviewUrl = action(CampaignWebviewController::class, $this->campaign->uuid);
    }

    /** @test */
    public function it_can_display_the_webview_for_a_campaign()
    {
        $this
            ->get($this->webviewUrl)
            ->assertSuccessful()
            ->assertSee('my webview html');
    }

    /** @test */
    public function it_will_not_display_a_webview_for_a_campaign_that_has_not_been_sent()
    {
        $this->withExceptionHandling();

        $this->campaign->update(['status' => CampaignStatus::DRAFT]);

        $this
            ->get($this->webviewUrl)
            ->assertStatus(404);
    }
}

<?php

namespace Spatie\EmailCampaigns\Tests;

use CreateEmailCampaignTables;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\EmailCampaigns\EmailCampaignsServiceProvider;
use Spatie\EmailCampaigns\Http\Controllers\TrackClicksController;
use Spatie\EmailCampaigns\Http\Controllers\TrackOpensController;
use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscriber;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/database/factories');

        Route::emailCampaigns('email-campaigns');
    }

    protected function getPackageProviders($app)
    {
        return [
            EmailCampaignsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        include_once __DIR__ . '/../database/migrations/create_email_campaign_tables.php.stub';

        (new CreateEmailCampaignTables())->up();
    }

    protected function simulateOpen(Collection $campaignSends)
    {
        $campaignSends->each(function (CampaignSend $campaignSend) {
            $this
                ->get(action(TrackOpensController::class, $campaignSend->uuid))
                ->assertSuccessful();
        });

        return $this;
    }

    public function simulateClick(CampaignLink $campaignLink, Collection $subscribers)
    {
        $subscribers->each(function(Subscriber $subscriber) use ($campaignLink) {
           $this
                ->get(action(TrackClicksController::class, [
                    $campaignLink->uuid,
                    $subscriber->uuid,
                ]))
                ->assertRedirect();
        });
    }
}

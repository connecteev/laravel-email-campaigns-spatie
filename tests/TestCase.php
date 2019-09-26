<?php

namespace Spatie\EmailCampaigns\Tests;

use CreateEmailCampaignTables;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\EmailCampaigns\EmailCampaignsServiceProvider;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/database/factories');
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

        include_once __DIR__.'/../database/migrations/create_email_campaign_tables.php.stub';

        (new CreateEmailCampaignTables())->up();
    }
}

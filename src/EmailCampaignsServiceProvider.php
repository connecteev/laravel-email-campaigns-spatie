<?php

namespace Spatie\EmailCampaigns;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\EmailCampaigns\Commands\CalculateStatisticsCommand;
use Spatie\EmailCampaigns\Http\Controllers\TrackOpensController;
use Spatie\EmailCampaigns\Http\Controllers\TrackClicksController;
use Spatie\EmailCampaigns\Http\Controllers\UnsubscribeController;
use Spatie\EmailCampaigns\Http\Controllers\CampaignWebviewController;
use Spatie\EmailCampaigns\Http\Controllers\ConfirmSubscriptionController;

class EmailCampaignsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this
            ->registerViews()
            ->registerPublishables()
            ->registerRoutes();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/email-campaigns.php', 'email-campaigns');

        $this->registerCommands();
    }

    public function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'email-campaigns');

        return $this;
    }

    public function registerPublishables()
    {
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/email-campaigns'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../config/email-campaigns.php' => config_path('email-campaigns.php'),
        ], 'config');

        if (! class_exists('CreateEmailCampaignTables')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_email_campaign_tables.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_email_campaigns.php'),
            ], 'migrations');
        }

        return $this;
    }

    protected function registerRoutes()
    {
        Route::macro('emailCampaigns', function () {
            Route::get('/confirm-subscription/{subscriptionUuid}', ConfirmSubscriptionController::class);
            Route::get('/unsubscribe/{subscriptionUuid}', UnsubscribeController::class);

            Route::get('/track-clicks/{campaignLinkUuid}/{subscriberUuid}', TrackClicksController::class);
            Route::get('/track-opens/{campaignSendUuid}', TrackOpensController::class);

            Route::get('webview/{campaignUuid}', CampaignWebviewController::class);
        });

        return $this;
    }

    protected function registerCommands()
    {
        $this->app->bind('command.email-campaigns:calculate-statistics', CalculateStatisticsCommand::class);

        $this->commands([
            'command.email-campaigns:calculate-statistics',
        ]);

        return $this;
    }
}

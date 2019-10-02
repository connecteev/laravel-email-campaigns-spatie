<?php

namespace Spatie\EmailCampaigns;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\EmailCampaigns\Actions\ConfirmSubscriptionAction;
use Spatie\EmailCampaigns\Actions\SubscribeAction;
use Spatie\EmailCampaigns\Exceptions\InvalidConfig;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;
use Spatie\EmailCampaigns\Http\Controllers\ConfirmSubscriptionController;
use Spatie\EmailCampaigns\Http\Controllers\TrackClicksController;
use Spatie\EmailCampaigns\Http\Controllers\UnsubscribeController;

class EmailCampaignsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/email-campaigns.php' => config_path('email-campaigns.php'),
        ], 'config');

        if (! class_exists('CreateEmailCampaignTables')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_email_campaign_tables.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_email_campaigns.php'),
            ], 'migrations');
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'email-campaigns');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/email-campaigns'),
        ], 'views');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/email-campaigns.php', 'email-campaigns');

        $this
            ->registerRoutes()
            ->registerActions();
    }

    protected function registerRoutes()
    {
        Route::get('/confirm-subscription/{subscriptionUuid}', ConfirmSubscriptionController::class);
        Route::get('/track-clicks/{campaignLinkUuid}/{subscriberUuid?}', TrackClicksController::class);
        Route::get('/unsubscribe/{subscriptionUuid}', UnsubscribeController::class);

        return $this;
    }

    protected function registerActions()
    {
        $this
            ->registerAction('personalize_html', PersonalizeHtmlAction::class)
            ->registerAction('prepare_email_html', PrepareEmailHtmlAction::class)
            ->registerAction('subscribe_action', SubscribeAction::class)
            ->registerAction('confirm_subscription', ConfirmSubscriptionAction::class);
    }

    private function registerAction(string $actionName, string $actionClass)
    {
        $configuredClass = config("email-campaigns.actions.{$actionName}");

        if (! is_a($configuredClass, $actionClass, true)) {
            throw InvalidConfig::invalidAction($actionName, $configuredClass ?? '', $actionClass);
        }

        $this->app->bind($actionClass, function () use ($configuredClass) {
            return new $configuredClass;
        });

        return $this;
    }
}

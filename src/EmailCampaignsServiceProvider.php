<?php

namespace Spatie\EmailCampaigns;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;
use Spatie\EmailCampaigns\Exceptions\InvalidConfig;
use Spatie\EmailCampaigns\Http\Controllers\TrackClicksController;
use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;
use Spatie\MediaLibrary\Commands\CleanCommand;
use Spatie\MediaLibrary\Commands\ClearCommand;
use Spatie\MediaLibrary\Filesystem\Filesystem;
use Spatie\MediaLibrary\Commands\RegenerateCommand;
use Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\WidthCalculator;
use Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\TinyPlaceholderGenerator;

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
        Route::get('/track-clicks/{campaignLinkUuid}/{subscriberUuid?}',TrackClicksController::class);

        return $this;
    }

    protected function registerActions()
    {
        $prepareEmailHtmlActionClass = config('email-campaigns.actions.prepare_email_html');

        if (! is_a($prepareEmailHtmlActionClass, PrepareEmailHtmlAction::class, true)) {
            throw InvalidConfig::invalidPrepareEmailAction($prepareEmailHtmlActionClass);
        }

        $this->app->bind(PrepareEmailHtmlAction::class, function() use ($prepareEmailHtmlActionClass) {
            return new $prepareEmailHtmlActionClass;
        });

        $personalizeHtmlActionClass = config('email-campaigns.actions.personalize_html');

        if (! is_a($personalizeHtmlActionClass, PersonalizeHtmlAction::class, true)) {
            throw InvalidConfig::invalidPersonalizeHtmlAction($personalizeHtmlActionClass);
        }

        $this->app->bind(PersonalizeHtmlAction::class, function() use ($personalizeHtmlActionClass) {
            return new $personalizeHtmlActionClass;
        });
    }

}

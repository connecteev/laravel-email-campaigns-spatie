---
title: Installation & setup
weight: 4
---

This package can be installed via composer:

```bash
composer require "spatie/laravel-email-campaigns:^1.0.0"
```
You need to publish and run the migration:

```bash
php artisan vendor:publish --provider="Spatie\EmailCampaigns\EmailCampaignsServiceProvider" --tag="migrations"
php artisan migrate
```

You must use register the routes needed to handle subscription confirmations, open and click tracking. You can do that by adding this macro to your routes file.

```php
Route::emailCampaigns('email-campaigns');
```

In the console kernel you should schedule the `email-campaigns:calculate-statistics` to run every minute.
```
// in app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // ...
    $schedule->command('email-campaigns:calculate-statistics')->everyMinute();
}
```

Most e-mail providers have a limit on how many mails you can send within a given amount of time. To throttle mails, this package uses redis. Make sure that is available on your system.

You must publish the config file with this command.

```bash
php artisan vendor:publish --provider="Spatie\EmailCampaigns\EmailCampaignsServiceProvider" --tag="config"
```

This is the default content of the config file:

```php
return [

    /*
     * You can customize some of the behaviour of this package by using our own custom action.
     * Your custom action should always extend the one of the default ones.
     *
     * Read the documention for more info: @TODO: add link
     */
    'actions' => [
        'personalize_html' => \Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction::class,
        'prepare_email_html' => \Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction::class,
        'subscribe_action' => \Spatie\EmailCampaigns\Actions\SubscribeAction::class,
        'confirm_subscription' => \Spatie\EmailCampaigns\Actions\ConfirmSubscriptionAction::class,
    ],

    /*
     * By default only 5 mails per second will be sent to avoid overwhelming your
     * e-mail sending service. To use this feature you must have Redis installed.
     */
    'throttling' => [
        'enabled' => true,
        'redis_connection_name' => '',
        'redis_key' => 'laravel-email-campaigns',
        'allowed_number_of_jobs_in_timespan' => 5,
        'timespan_in_seconds' => 1,
        'release_in_seconds' => 5,
    ],
];
```

Make sure to specify a valid connection name in the `throttling.redis_connection_name` key.

---
title: Installation & setup
weight: 4
---

This package can be installed via composer:

```bash
composer require "spatie/laravel-email-campaigns:^1.0.0"
```

## Choosing a mail driver

You should configure your Laravel to use one of [the many available mail drivers](https://laravel.com/docs/master/mail#driver-prerequisites). All emails that are sent via this package will use that default driver.

## Prepare the database

You need to publish and run the migration:

```bash
php artisan vendor:publish --provider="Spatie\EmailCampaigns\EmailCampaignsServiceProvider" --tag="migrations"
php artisan migrate
```

## Add the route macro

You must register the routes needed to handle subscription confirmations, open, and click tracking. You can do that by adding this macro to your routes file.

```php
Route::emailCampaigns('email-campaigns');
```

## Schedule the calculate statistics command

In the console kernel you should schedule the `email-campaigns:calculate-statistics` to run every minute.
```
// in app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // ...
    $schedule->command('email-campaigns:calculate-statistics')->everyMinute();
}
```

## Publish the config file

You must publish the config file with this command.

```bash
php artisan vendor:publish --provider="Spatie\EmailCampaigns\EmailCampaignsServiceProvider" --tag="config"
```

This is the default content of the config file:

```php
return [

    /*
     * Here you can specify which jobs should run on which queues.
     * Use an empty string to use the default queue.
     */
    'perform_on_queue' => [
        'calculate_statistics_job' => '',
        'register_click_job' => '',
        'register_open_job' => '',
        'send_campaign_job' => '',
        'send_mail_job' => '',
        'send_test_mail_job' => '',
    ],

    /*
     * By default only 5 mails per second will be sent to avoid overwhelming your
     * e-mail sending service. To use this feature you must have Redis installed.
     */
    'throttling' => [
        'enabled' => false,
        'redis_connection_name' => 'default',
        'redis_key' => 'laravel-email-campaigns',
        'allowed_number_of_jobs_in_timespan' => 5,
        'timespan_in_seconds' => 1,
        'release_in_seconds' => 5,
    ],

    /*
       * You can customize some of the behavior of this package by using our own custom action.
       * Your custom action should always extend the one of the default ones.
       */
    'actions' => [
        'personalize_html_action' => \Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction::class,
        'prepare_email_html_action' => \Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction::class,
        'prepare_webview_html_action' => \Spatie\EmailCampaigns\Actions\PrepareWebviewHtmlAction::class,
        'subscribe_action' => \Spatie\EmailCampaigns\Actions\SubscribeAction::class,
        'confirm_subscription_action' => \Spatie\EmailCampaigns\Actions\ConfirmSubscriptionAction::class,
    ],
];
```

## Install and configure redis

Most e-mail providers limit how many mails you can send within a given amount of time. To throttle mails, this package uses Redis. Make sure Redis is available on your system. You must specify a valid Redis connection name in the `throttling.redis_connection_name` key.

By default, we set this value to the default Laravel connection name, `default`.

## Prepare the queues

Many tasks performed by this package are queued. Make sure you don't use `sync` but [a real queue driver](https://laravel.com/docs/master/queues#driver-prerequisites).

In the `perform_on_queue` key of the `email-campaigns` config file, you can specify which jobs should be performed on which queues. The `register_click_job`, `register_open_job`, and `send_mail_job` jobs could receive a large number of jobs. Using only one queue potentially results in a long wait for other jobs. So we recommend using a separate queue for the `register_click_job`, `register_open_job`, and `send_mail_job` jobs.

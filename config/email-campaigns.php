<?php

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
        'enabled' => false,
        'redis_connection_name' => '',
        'redis_key' => 'laravel-email-campaigns',
        'allowed_number_of_jobs_in_timespan' => 5,
        'timespan_in_seconds' => 1,
        'release_in_seconds' => 5,
    ],
];

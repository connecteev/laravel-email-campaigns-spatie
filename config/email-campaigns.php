<?php

return [
    'actions' => [
        'personalize_html' => \Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction::class,
        'prepare_email_html' => \Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction::class,
        'subscribe_action' => \Spatie\EmailCampaigns\Actions\SubscribeAction::class,
        'confirm_subscription' => \Spatie\EmailCampaigns\Actions\ConfirmSubscriptionAction::class,
    ],

    'throttling' => [
        'enabled' => false,
        'redis_connection_name' => '',
        'redis_key' => 'laravel-email-campaigns',
        'timespan_in_seconds' => 1,
        'allowed_number_of_jobs_in_timespan' => 5,
        'release_in_seconds' => 5,
    ]
];

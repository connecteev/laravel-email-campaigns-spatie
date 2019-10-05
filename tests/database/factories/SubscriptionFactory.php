<?php

use Faker\Generator;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Models\Subscription;

$factory->define(Subscription::class, function (Generator $faker) {
    return [
        'email_list_id' => factory(EmailList::class),
        'email_list_subscriber_id' => factory(Subscriber::class),
        'status' => SubscriptionStatus::SUBSCRIBED,
    ];
});

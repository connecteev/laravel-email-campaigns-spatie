<?php

use Faker\Generator;
use Spatie\EmailCampaigns\Enums\EmailListSubscriptionStatus;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Models\EmailListSubscription;

$factory->define(EmailListSubscription::class, function (Generator $faker) {
    return [
        'email_list_id' => factory(EmailList::class),
        'email_list_subscriber_id' => factory(Subscriber::class),
        'status' => EmailListSubscriptionStatus::SUBSCRIBED,
    ];
});



<?php

use Faker\Generator;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;
use Spatie\EmailCampaigns\Models\EmailListSubscription;

$factory->define(EmailListSubscription::class, function (Generator $faker) {
    return [
        'email_list_id' => factory(EmailList::class),
        'email_list_subscriber_id' => factory(EmailListSubscriber::class),
    ];
});


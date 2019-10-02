<?php

use Faker\Generator;
use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Models\EmailCampaignSend;
use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Models\Subscription;

$factory->define(EmailCampaignSend::class, function (Generator $faker) {
    return [
        'email_campaign_id' => factory(EmailCampaign::class),
        'email_list_subscription_id' => factory(Subscription::class),
    ];
});

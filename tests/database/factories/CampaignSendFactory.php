<?php

use Faker\Generator;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Models\Subscription;

$factory->define(CampaignSend::class, function (Generator $faker) {
    return [
        'email_campaign_id' => factory(Campaign::class),
        'email_list_subscription_id' => factory(Subscription::class),
    ];
});

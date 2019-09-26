<?php

use Faker\Generator;
use Spatie\EmailCampaigns\Models\EmailCampaignSend;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;

$factory->define(EmailCampaignSend::class, function (Generator $faker) {
    return [
        'email_campaign_id' => factory(\Spatie\EmailCampaigns\Models\EmailCampaign::class),
        'email_list_subscriber_id' => factory(EmailListSubscriber::class),
    ];
});

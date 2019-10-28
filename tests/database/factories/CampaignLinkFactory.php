<?php

namespace Spatie\EmailCampaigns\Tests\database\factories;

use Faker\Generator;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\CampaignLink;

$factory->define(CampaignLink::class, function (Generator $faker) {
    return [
        'email_campaign_id' => factory(Campaign::class),
        'original_link' => $faker->url,
    ];
});

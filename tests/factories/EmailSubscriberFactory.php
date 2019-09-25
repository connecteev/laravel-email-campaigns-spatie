<?php

use Faker\Generator;
use Spatie\EmailCampaigns\Models\EmailCampaign;

$factory->define(\Spatie\EmailCampaigns\Models\EmailListSubscriber::class, function (Generator $faker) {
    return [
        'email' => $faker->email,
    ];
});


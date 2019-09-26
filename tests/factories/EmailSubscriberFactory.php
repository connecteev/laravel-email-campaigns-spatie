<?php

use Faker\Generator;

$factory->define(\Spatie\EmailCampaigns\Models\EmailListSubscriber::class, function (Generator $faker) {
    return [
        'email' => $faker->email,
    ];
});

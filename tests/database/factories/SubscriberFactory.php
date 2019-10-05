<?php

use Faker\Generator;

$factory->define(\Spatie\EmailCampaigns\Models\Subscriber::class, function (Generator $faker) {
    return [
        'email' => $faker->email,
        'uuid' => $faker->uuid,
    ];
});

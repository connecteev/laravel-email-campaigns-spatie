<?php

use Faker\Generator;
use Spatie\EmailCampaigns\Models\Campaign;

$factory->define(Campaign::class, function (Generator $faker) {
    return [
        'subject' => $faker->sentence,
        'html' => $faker->randomHtml(),
        'track_opens' => $faker->boolean,
        'track_clicks' => $faker->boolean,
    ];
});

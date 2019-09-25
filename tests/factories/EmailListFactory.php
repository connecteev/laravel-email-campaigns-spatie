<?php

use Faker\Generator;
use Spatie\EmailCampaigns\Models\EmailCampaign;

$factory->define(\Spatie\EmailCampaigns\Models\EmailList::class, function (Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});


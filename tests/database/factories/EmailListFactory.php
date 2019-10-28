<?php

use Faker\Generator;

$factory->define(\Spatie\EmailCampaigns\Models\EmailList::class, function (Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

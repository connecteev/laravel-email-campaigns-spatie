<?php

namespace Spatie\EmailCampaigns\Tests\Factories;

use Illuminate\Support\Collection;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;

class EmailListFactory
{
    /** @var int */
    private $subscriberCount = 0;

    public function withSubscriberCount(int $subscriberCount)
    {
        $this->subscriberCount = $subscriberCount;
    }

    public function create(array $attributes = [])
    {
        $emailList = factory(EmailList::class)->create($attributes);

        Collection::times(3)->each(function(int $i) {
            factory(EmailListSubscriber::class)->create(['email' => "subscriber{$i}@example.com"]);
        });
    }
}


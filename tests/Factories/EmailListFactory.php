<?php

namespace Spatie\EmailCampaigns\Tests\Factories;

use Illuminate\Support\Collection;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscriber;

class EmailListFactory
{
    /** @var int */
    private $subscriberCount = 0;

    public function withSubscriberCount(int $subscriberCount)
    {
        $this->subscriberCount = $subscriberCount;

        return $this;
    }

    public function create(array $attributes = []): EmailList
    {
        $emailList = factory(EmailList::class)->create($attributes);

        Collection::times($this->subscriberCount)
            ->map(function (int $i) {
                return factory(Subscriber::class)->create(['email' => "subscriber{$i}@example.com"]);
            })
            ->each(function(Subscriber $subscriber) use ($emailList) {
                $subscriber->subscribeTo($emailList);
            });

        return $emailList->refresh();
    }
}


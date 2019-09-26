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

        return $this;
    }

    public function create(array $attributes = []): EmailList
    {
        $emailList = factory(EmailList::class)->create($attributes);

        Collection::times($this->subscriberCount)
            ->map(function (int $i) {
                return factory(EmailListSubscriber::class)->create(['email' => "subscriber{$i}@example.com"]);
            })
            ->each(function(EmailListSubscriber $emailListSubscriber) use ($emailList) {
                $emailListSubscriber->subscribeTo($emailList);
            });

        return $emailList->refresh();
    }
}


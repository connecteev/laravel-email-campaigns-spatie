<?php

namespace Spatie\EmailCampaigns\Tests\Factories;

use Spatie\EmailCampaigns\Models\Campaign;

class EmailCampaignFactory
{
    /** @var int */
    private $subscriberCount;

    public function withSubscriberCount(int $subscriberCount)
    {
        $this->subscriberCount = $subscriberCount;

        return $this;
    }

    public function create(array $attributes = []): Campaign
    {
        $emailList = (new EmailListFactory())
            ->withSubscriberCount($this->subscriberCount)
            ->create();

        $campaign = factory(Campaign::class)
            ->create($attributes)
            ->to($emailList);

        return $campaign->refresh();
    }
}


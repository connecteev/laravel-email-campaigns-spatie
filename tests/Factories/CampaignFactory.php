<?php

namespace Spatie\EmailCampaigns\Tests\Factories;

use Carbon\Carbon;
use Spatie\EmailCampaigns\Models\Campaign;

class CampaignFactory
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

    public static function createSentAt(string $dateTime): Campaign
    {
        return factory(Campaign::class)->create([
            'sent_at' => Carbon::createFromFormat('Y-m-d H:i:s', $dateTime),
        ]);
    }
}


<?php

namespace Spatie\EmailCampaigns\Tests\TestClasses;

use Illuminate\Database\Eloquent\Builder;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Support\Segments\Segment;

class TestSegmentQueryOnlyJohn extends Segment
{
    public function getSubscriptionsQuery(Campaign $campaign): Builder
    {
        return Subscription::query()
            ->where('status', SubscriptionStatus::SUBSCRIBED)
            ->whereHas('subscriber', function (Builder $query) {
                $query->where('email', 'john@example.com');
            })
            ->where('email_list_id', $campaign->emailList->id);
    }
}

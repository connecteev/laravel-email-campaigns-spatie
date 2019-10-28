<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EmailCampaigns\Support\Config;
use Spatie\EmailCampaigns\Events\Unsubscribed;
use Spatie\EmailCampaigns\Models\Concerns\HasUuid;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EmailCampaigns\Actions\ConfirmSubscriptionAction;
use Spatie\EmailCampaigns\Http\Controllers\UnsubscribeController;

class Subscription extends Model
{
    public $table = 'email_list_subscriptions';

    use HasUuid;

    public $guarded = [];

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(EmailList::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class, 'email_list_subscriber_id');
    }

    public function confirm()
    {
        $action = Config::getActionClass('confirm_subscription_action', ConfirmSubscriptionAction::class);

        return $action->execute($this);
    }

    public function markAsUnsubscribed(CampaignSend $campaignSend = null)
    {
        $this->update(['status' => SubscriptionStatus::UNSUBSCRIBED]);

        if ($campaignSend) {
            CampaignUnsubscribe::firstOrCreate([
                'email_campaign_id' => $campaignSend->campaign->id,
                'email_list_subscriber_id' => $campaignSend->subscription->subscriber->id,
            ]);
        }

        event(new Unsubscribed($this, $campaignSend));

        return $this;
    }

    public function unsubscribeUrl(CampaignSend $campaignSend = null): string
    {
        return url(action(UnsubscribeController::class, [$this->uuid, optional($campaignSend)->uuid]));
    }
}

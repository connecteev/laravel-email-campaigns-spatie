<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EmailCampaigns\Support\Config;
use Spatie\EmailCampaigns\Events\Unsubscribed;
use Spatie\EmailCampaigns\Models\Concerns\HasUuid;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EmailCampaigns\Actions\ConfirmSubscriptionAction;

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
        $action = Config::getActionClass('confirm_subscription', ConfirmSubscriptionAction::class);

        return $action->execute($this);
    }

    public function markAsUnsubscribed()
    {
        $this->update(['status' => SubscriptionStatus::UNSUBSCRIBED]);

        event(new Unsubscribed($this));

        return $this;
    }
}

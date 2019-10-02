<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EmailCampaigns\Actions\ConfirmSubscriptionAction;
use Spatie\EmailCampaigns\Enums\EmailListSubscriptionStatus;
use Spatie\EmailCampaigns\Models\Concerns\HasUuid;

class EmailListSubscription extends Model
{
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
        return app(ConfirmSubscriptionAction::class)->execute($this);
    }

    public function markAsUnsubscribed()
    {
        $this->update(['status' => EmailListSubscriptionStatus::UNSUBSCRIBED]);

        return $this;
    }
}

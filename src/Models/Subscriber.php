<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EmailCampaigns\Support\Config;
use Spatie\EmailCampaigns\Actions\SubscribeAction;
use Spatie\EmailCampaigns\Models\Concerns\HasUuid;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\EmailCampaigns\Models\Concerns\HasExtraAttributes;

class Subscriber extends Model
{
    use HasUuid,
        HasExtraAttributes;

    public $table = 'email_list_subscribers';

    public $casts = [
        'extra_attributes' => 'array',
    ];

    protected $guarded = [];

    public static function findForEmail(string $email): ?Subscriber
    {
        return static::where('email', $email)->first();
    }

    public function emailLists(): BelongsToMany
    {
        return $this
            ->belongsToMany(EmailList::class, 'email_list_subscriptions','email_list_subscriber_id',  'email_list_id')
            ->where('email_list_subscriptions.status', SubscriptionStatus::SUBSCRIBED);
    }

    public function subscribeTo(EmailList $emailList, bool $respectDoubleOptIn = true): Subscription
    {
        $action = Config::getActionClass('subscribe_action', SubscribeAction::class);

        return $action->execute($this, $emailList, $respectDoubleOptIn);
    }

    public function subscribeNowTo(EmailList $emailList)
    {
        return $this->subscribeTo($emailList, false);
    }

    public function unsubscribeFrom(EmailList $emailList): void
    {
        $emailList->unsubscribe($this->email);
    }

    public function isSubscribedTo(EmailList $emailList): bool
    {
        return Subscription::query()
            ->where('email_list_subscriber_id', $this->id)
            ->where('email_list_id', $emailList->id)
            ->where('status', SubscriptionStatus::SUBSCRIBED)
            ->exists();
    }
}

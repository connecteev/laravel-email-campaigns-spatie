<?php

namespace Spatie\EmailCampaigns\Actions;

use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Events\Subscribed;
use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Mails\ConfirmSubscriptionMail;

class SubscribeAction
{
    public function execute(Subscriber $subscriber, EmailList $emailList, bool $respectDoubleOptIn = false): Subscription
    {
        $status = SubscriptionStatus::SUBSCRIBED;

        if ($respectDoubleOptIn) {
            if ($emailList->requires_double_opt_in) {
                $status = SubscriptionStatus::PENDING;
            }
        }

        if ($subscriber->isSubscribedTo($emailList)) {
            return $emailList->getSubscription($subscriber);
        }

        $subscription = Subscription::updateOrCreate(
            [
                'email_list_subscriber_id' => $subscriber->id,
                'email_list_id' => $emailList->id,
            ],
            [
                'status' => $status,
            ],
            );

        if ($subscription->status === SubscriptionStatus::PENDING) {
            Mail::to($subscriber->email)->send(new ConfirmSubscriptionMail($subscription));
        }

        if ($subscription->status === SubscriptionStatus::SUBSCRIBED) {
            event(new Subscribed($subscription));
        }

        return $subscription;
    }
}

<?php

namespace Spatie\EmailCampaigns\Actions;

use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Enums\EmailListSubscriptionStatus;
use Spatie\EmailCampaigns\Mails\ConfirmSubscriptionMail;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscriber;
use Spatie\EmailCampaigns\Models\EmailListSubscription;

class SubscribeAction
{
    public function execute(Subscriber $subscriber, EmailList $emailList): EmailListSubscription
    {
        $status = EmailListSubscriptionStatus::SUBSCRIBED;

        if ($emailList->requires_double_opt_in) {
            $status = EmailListSubscriptionStatus::PENDING;
        }

        if ($subscriber->isSubscribedTo($emailList)) {
            return $emailList->getSubscription($subscriber);
        }

        $subscription = EmailListSubscription::updateOrCreate(
            [
                'email_list_subscriber_id' => $subscriber->id,
                'email_list_id' => $emailList->id,
            ],
            [
                'status' => $status,
            ],
            );

        if ($subscription->status === EmailListSubscriptionStatus::PENDING) {
            Mail::to($subscriber->email)->send(new ConfirmSubscriptionMail($subscription));
        }

        return $subscription;
    }
}


<?php

namespace Spatie\EmailCampaigns\Actions;

use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Enums\EmailListSubscriptionStatus;
use Spatie\EmailCampaigns\Mails\ConfirmSubscriptionMail;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;
use Spatie\EmailCampaigns\Models\EmailListSubscription;

class SubscribeAction
{
    public function execute(EmailListSubscriber $subscriber, EmailList $emailList): EmailListSubscription
    {
        $status = EmailListSubscriptionStatus::SUBSCRIBED;

        if ($emailList->requires_double_opt_in) {
            $status = EmailListSubscriptionStatus::PENDING;
        }

        $subscription = EmailListSubscription::firstOrCreate([
            'email_list_subscriber_id' => $subscriber->id,
            'email_list_id' => $emailList->id,
            'status' => $status,
        ]);

        if ($emailList->requires_double_opt_in) {
            Mail::to($subscriber->email)->send(new ConfirmSubscriptionMail($subscription));
        }

        return $subscription;
    }
}


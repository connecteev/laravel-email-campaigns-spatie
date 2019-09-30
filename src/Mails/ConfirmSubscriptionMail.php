<?php

namespace Spatie\EmailCampaigns\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\EmailCampaigns\Http\Controllers\ConfirmSubscriptionController;
use Spatie\EmailCampaigns\Models\EmailListSubscription;

class ConfirmSubscriptionMail extends Mailable implements ShouldQueue
{
    /** @var \Spatie\EmailCampaigns\Models\EmailListSubscription */
    public $subscription;

    public $confirmationUrl;

    public function __construct(EmailListSubscription $subscription)
    {
        $this->subscription = $subscription;

        $this->confirmationUrl = action(ConfirmSubscriptionController::class, $subscription);
    }

    public function build()
    {
        return $this
            ->subject("Confirm your subscription to {$this->subscription->emailList->name}")
            ->markdown('email-campaigns::mails.confirmSubscription');
    }
}


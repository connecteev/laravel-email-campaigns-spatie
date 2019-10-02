<?php

namespace Spatie\EmailCampaigns\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\EmailCampaigns\Http\Controllers\ConfirmSubscriptionController;
use Spatie\EmailCampaigns\Models\Subscription;

class ConfirmSubscriptionMail extends Mailable implements ShouldQueue
{
    /** @var \Spatie\EmailCampaigns\Models\Subscription */
    public $subscription;

    public $confirmationUrl;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;

        $this->confirmationUrl = url(action(ConfirmSubscriptionController::class, $subscription->uuid));
    }

    public function build()
    {
        return $this
            ->subject("Confirm your subscription to {$this->subscription->emailList->name}")
            ->markdown('email-campaigns::mails.confirmSubscription');
    }
}


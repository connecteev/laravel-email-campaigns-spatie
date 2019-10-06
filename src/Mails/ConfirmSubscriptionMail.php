<?php

namespace Spatie\EmailCampaigns\Mails;

use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Http\Controllers\ConfirmSubscriptionController;

class ConfirmSubscriptionMail extends Mailable implements ShouldQueue
{
    /** @var \Spatie\EmailCampaigns\Models\Subscription */
    public $subscription;

    /** @var string */
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

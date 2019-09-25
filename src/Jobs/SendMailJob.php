<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;
use Spatie\EmailCampaigns\Models\EmailCampaignSend;

class SendMailJob
{
    /** @var \Spatie\EmailCampaigns\Models\EmailCampaignSend */
    private $pendingSend;

    public function __construct(EmailCampaignSend $pendingSend)
    {
        $this->pendingSend = $pendingSend;
    }

    public function handle()
    {
        $personalisedHtml = (new PersonalizeHtmlAction())->handle(
            $this->pendingSend->emailCampaign->email_html,
            $this->pendingSend->emailSubscriber,
            $this->pendingSend->emailCampaig,
            );

        Mail::raw($personalisedHtml, function (Message $message) {
            $message
                ->to($this->pendingSend->emailSubscriber->email)
                ->subject($this->pendingSend->emailCampaign->subject);
        });

        $this->pendingSend->markAsSent();
    }
}


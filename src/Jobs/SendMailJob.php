<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;
use Spatie\EmailCampaigns\Models\EmailCampaignSend;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    /** @var \Spatie\EmailCampaigns\Models\EmailCampaignSend */
    public $pendingSend;

    public function __construct(EmailCampaignSend $pendingSend)
    {
        $this->pendingSend = $pendingSend;
    }

    public function handle()
    {
        if ($this->pendingSend->wasAlreadySent()) {
            return;
        }

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


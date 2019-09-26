<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\EmailCampaigns\Mails\CampaignMail;
use Spatie\EmailCampaigns\Models\EmailCampaignSend;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;

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
            $this->pendingSend->emailListSubscriber,
            $this->pendingSend->emailCampaign,
            );

        $campaignMail = new CampaignMail($this->pendingSend->emailCampaign->subject, $personalisedHtml);

        Mail::to($this->pendingSend->emailListSubscriber->email)->send($campaignMail);

        $this->pendingSend->markAsSent();
    }
}

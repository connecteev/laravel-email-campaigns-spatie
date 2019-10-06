<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;
use Spatie\EmailCampaigns\Mails\CampaignMail;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Support\Config;

class SendTestMailJob
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    public $campaign;

    /** @var string */
    public $email;

    public function __construct(Campaign $campaign, string $email)
    {
        $this->campaign = $campaign;

        $this->email = $email;
    }

    public function handle()
    {
        $campaignMail = new CampaignMail($this->campaign->subject, $this->campaign->html);

        Mail::to($this->email)->send($campaignMail);
    }
}

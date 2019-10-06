<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\EmailCampaigns\Models\Campaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\EmailCampaigns\Mails\CampaignMail;

class SendTestMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    public $campaign;

    /** @var string */
    public $email;

    /** @var string */
    public $queue;

    public function __construct(Campaign $campaign, string $email)
    {
        $this->campaign = $campaign;

        $this->email = $email;

        $this->queue = config('email-campaigns.perform_on_queue.send_test_mail_job');
    }

    public function handle()
    {
        $campaignMail = new CampaignMail($this->campaign->subject, $this->campaign->html);

        Mail::to($this->email)->send($campaignMail);
    }
}

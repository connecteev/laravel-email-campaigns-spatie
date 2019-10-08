<?php

namespace Spatie\EmailCampaigns\Commands;

use Illuminate\Console\Command;
use Spatie\EmailCampaigns\Jobs\SendMailJob;
use Spatie\EmailCampaigns\Models\CampaignSend;

class RetryPendingSendsCommand extends Command
{
    public $signature = 'email-campaigns:retry-pending-sends';

    public $description = 'Dispatch a job for each MailSend that has not been sent yet';

    public function handle()
    {
        $pendingCampaignSendCount = CampaignSend::whereNull('sent_at')->count();

        $this->comment("Dispatching jobs for {$pendingCampaignSendCount} pending CampaignSends");

        CampaignSend::whereNull('sent_at')->each(function(CampaignSend $campaignSend) {
            dispatch(new SendMailJob($campaignSend));
        });

        $this->comment('All done!');
    }
}

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
use Spatie\RateLimitedMiddleware\RateLimited;

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
            $this->pendingSend->campaign->email_html,
            $this->pendingSend->subscription,
            $this->pendingSend->campaign,
            );

        $campaignMail = new CampaignMail($this->pendingSend->campaign->subject, $personalisedHtml);

        Mail::to($this->pendingSend->subscription->subscriber->email)->send($campaignMail);

        $this->pendingSend->markAsSent();
    }

    public function middleware()
    {
        $throttlingConfig = config('email-campaigns.throttling');

        $rateLimitedMiddleware = (new RateLimited())
            ->enabled($throttlingConfig['enabled'])
            ->connectionName($throttlingConfig['redis_connection_name'])
            ->key($throttlingConfig['redis_key'])
            ->timespanInSeconds($throttlingConfig['timespan_in_seconds'])
            ->allowedNumberOfJobsInTimeSpan($throttlingConfig['allowed_number_of_jobs_in_timespan']);

        return [$rateLimitedMiddleware];
    }
}

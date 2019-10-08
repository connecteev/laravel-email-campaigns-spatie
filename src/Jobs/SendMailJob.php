<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\EmailCampaigns\Support\Config;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\EmailCampaigns\Mails\CampaignMail;
use Spatie\RateLimitedMiddleware\RateLimited;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Events\CampaignMailSent;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    /** @var \Spatie\EmailCampaigns\Models\CampaignSend */
    public $pendingSend;

    /** @var string */
    public $queue;

    public function __construct(CampaignSend $pendingSend)
    {
        $this->pendingSend = $pendingSend;

        $this->queue = config('email-campaigns.perform_on_queue.send_mail_job');
    }

    public function handle()
    {
        if ($this->pendingSend->wasAlreadySent()) {
            return;
        }

        $action = Config::getActionClass('personalize_html_action', PersonalizeHtmlAction::class);

        $personalisedHtml = $action->execute(
            $this->pendingSend->campaign->email_html,
            $this->pendingSend,
            );

        $campaignMail = new CampaignMail(
            $this->pendingSend->campaign->subject,
            $personalisedHtml,
            $this->pendingSend,
        );

        Mail::to($this->pendingSend->subscription->subscriber->email)->send($campaignMail);

        $this->pendingSend->markAsSent();

        event(new CampaignMailSent($this->pendingSend));
    }

    public function middleware()
    {
        $throttlingConfig = config('email-campaigns.throttling');

        $rateLimitedMiddleware = (new RateLimited())
            ->enabled($throttlingConfig['enabled'])
            ->connectionName($throttlingConfig['redis_connection_name'])
            ->allow($throttlingConfig['allowed_number_of_jobs_in_timespan'])
            ->everySeconds($throttlingConfig['timespan_in_seconds'])
            ->releaseAfterSeconds($throttlingConfig['release_in_seconds']);

        return [$rateLimitedMiddleware];
    }
}

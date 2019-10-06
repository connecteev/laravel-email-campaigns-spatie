<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\EmailCampaigns\Support\Config;
use Spatie\EmailCampaigns\Models\Campaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\EmailCampaigns\Events\CampaignSent;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    public $campaign;

    /** @var string */
    public $queue;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->queue = config('email-campaigns.perform_on_queue.send_campaign_job');
    }

    public function handle()
    {
        if ($this->campaign->wasAlreadySent()) {
            return;
        }

        $this
            ->prepareEmailHtml()
            ->prepareWebviewHtml()
            ->send();
    }

    protected function prepareEmailHtml()
    {
        $action = Config::getActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $action->execute($this->campaign);

        return $this;
    }

    private function prepareWebviewHtml()
    {
        $this->campaign->webview_html = $this->campaign->html;
        $this->campaign->save();

        return $this;
    }

    protected function send()
    {
        $this->campaign->emailList->subscriptions->each(function (Subscription $emailListSubscription) {
            $pendingSend = $this->campaign->sends()->create([
                'email_list_subscription_id' => $emailListSubscription->id,
                'uuid' => (string) Str::uuid(),
            ]);

            dispatch(new SendMailJob($pendingSend));
        });

        $this->campaign->markAsSent($this->campaign->emailList->subscriptions->count());

        event(new CampaignSent($this->campaign));
    }
}

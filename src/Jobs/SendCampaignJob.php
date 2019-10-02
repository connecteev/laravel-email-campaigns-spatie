<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Events\EmailCampaignSent;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;
use Spatie\EmailCampaigns\Models\EmailListSubscription;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \Spatie\EmailCampaigns\Models\EmailCampaign */
    public $campaign;

    public function __construct(EmailCampaign $campaign)
    {
        $this->campaign = $campaign;
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
        (new PrepareEmailHtmlAction())->execute($this->campaign);

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
        $this->campaign->emailList->subscriptions->each(function (EmailListSubscription $emailListSubscription) {
            $pendingSend = $this->campaign->sends()->create([
                'email_list_subscription_id' => $emailListSubscription->id,
            ]);

            dispatch(new SendMailJob($pendingSend));
        });

        $this->campaign->markAsSent();

        event(new EmailCampaignSent($this->campaign));
    }
}

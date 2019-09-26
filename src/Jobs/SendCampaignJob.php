<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;
use Spatie\EmailCampaigns\Events\EmailCampaignSent;
use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;

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
        $this
            ->prepareEmailHtml()
            ->prepareWebviewHtml()
            ->fillMailQueue()
            ->send();


        $this->makeLinksTrackable($this->campaign);
    }

    protected function prepareEmailHtml()
    {
        (new PrepareEmailHtmlAction())->execute($this->campaign);

        return $this;
    }

    private function prepareWebviewHtml()
    {
        $this->campaign->webview_html = $this->html;
        $this->campaign->save();

        return $this;
    }

    protected function send()
    {
        $this->campaign->emailList->subscribers->each(function (EmailListSubscriber $emailSubscriber) {
            $pendingSend = $this->campaign->sends()->create([
                'email_subscriber_id' => $emailSubscriber->id,
            ]);

            dispatch(new SendMailJob($pendingSend));
        });

        event(new EmailCampaignSent($this->campaign));
    }
}


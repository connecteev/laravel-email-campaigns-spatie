<?php

namespace Spatie\EmailCampaigns\Jobs;

use DOMDocument;
use DOMElement;
use Exception;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;
use Spatie\EmailCampaigns\Events\EmailCampaignSent;
use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;
use Symfony\Component\DomCrawler\Crawler;

class SendCampaignJob
{
    /** @var \Spatie\EmailCampaigns\Models\EmailCampaign */
    public $campaign;

    /** @var \Spatie\EmailCampaigns\EmailList */
    public $emailList;

    public function __construct(EmailCampaign $campaign, EmailList $emailList)
    {
        $this->campaign = $campaign;

        $this->emailList = $emailList;
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
        $this->emailList->subscribers->each(function (EmailListSubscriber $emailSubscriber) {
            $pendingSend = $this->campaign->sends()->create([
                'email_subscriber_id' => $emailSubscriber->id,
            ]);

            dispatch(new SendMailJob($pendingSend));
        });

        event(new EmailCampaignSent($this->campaign));

    }
}


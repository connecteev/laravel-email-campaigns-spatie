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
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Events\CampaignSent;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;
use Spatie\EmailCampaigns\Actions\PrepareWebviewHtmlAction;

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
        $action = Config::getActionClass('prepare_email_html_action', PrepareEmailHtmlAction::class);
        $action->execute($this->campaign);

        return $this;
    }

    private function prepareWebviewHtml()
    {
        $action = Config::getActionClass('prepare_webview_html_action', PrepareWebviewHtmlAction::class);
        $action->execute($this->campaign);

        return $this;
    }

    protected function send()
    {
        $segment = $this->campaign->getSegment();

        $subscriptionsQuery = $segment
            ->getSubscriptionsQuery($this->campaign)
            ->where('status', SubscriptionStatus::SUBSCRIBED)
            ->where('email_list_id', $this->campaign->emailList->id);

        $sentMailCount = 0;
        $subscriptionsQuery->each(function (Subscription $subscription) use (&$sentMailCount) {
            if (! $this->campaign->getSegment()->shouldSend($subscription, $this->campaign)) {
                return;
            }

            if (! $this->isValidSubscriptionForEmailList($subscription, $this->campaign->emailList)) {
                return;
            }

            $pendingSend = $this->campaign->sends()->create([
                'email_list_subscription_id' => $subscription->id,
                'uuid' => (string) Str::uuid(),
            ]);

            dispatch(new SendMailJob($pendingSend));

            $sentMailCount++;
        });

        $this->campaign->markAsSent($sentMailCount);

        event(new CampaignSent($this->campaign));
    }

    protected function isValidSubscriptionForEmailList(Subscription $subscription, EmailList $emailList): bool
    {
        if (! $subscription->status === SubscriptionStatus::SUBSCRIBED) {
            return false;
        }

        if ((int) $subscription->email_list_id !== (int) $emailList->id) {
            return false;
        }

        return true;
    }
}

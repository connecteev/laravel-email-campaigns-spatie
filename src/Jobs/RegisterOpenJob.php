<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\EmailCampaigns\Events\CampaignOpened;
use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Models\CampaignOpen;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Models\Subscriber;

class RegisterOpenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /** @var string */
    public $campaignSendUuid;

    public function __construct(string $campaignSendUuid)
    {
        $this->campaignSendUuid = $campaignSendUuid;
    }

    public function handle()
    {
        /** @var \Spatie\EmailCampaigns\Models\CampaignSend|null $campaignSend */
        if (! $campaignSend = CampaignSend::findByUuid($this->campaignSendUuid)) {
            return;
        }

        $campaignOpen = CampaignOpen::create([
            'campaign_id' => $campaignSend->campaign->id,
            'email_list_subscriber_id' => $campaignSend->subscription->subscriber->id,
        ]);

        event(new CampaignOpened($campaignOpen));
    }
}

<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\EmailCampaigns\Events\CampaignLinkClicked;
use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Models\Subscriber;

class RegisterClickJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /** @var string */
    public $campaignLinkUuid;

    /** @var string */
    public $subscriberUuid;

    public function __construct(string $campaignLinkUuid, string $subscriberUuid)
    {
        $this->campaignLinkUuid = $campaignLinkUuid;

        $this->subscriberUuid = $subscriberUuid;
    }

    public function handle()
    {
        /** @var \Spatie\EmailCampaigns\Models\CampaignLink|null $campaignLink */
        if (! $campaignLink = CampaignLink::findByUuid($this->campaignLinkUuid)) {
            return;
        }

        /** @var \Spatie\EmailCampaigns\Models\Subscriber|null $subscriber */
        if (! $subscriber = Subscriber::findByUuid($this->subscriberUuid)) {
            return;
        }

        $campaignClick = $campaignLink->clicks()->create([
            'email_list_subscriber_id' => $subscriber->id,
        ]);

        event(new CampaignLinkClicked($campaignClick));
    }
}

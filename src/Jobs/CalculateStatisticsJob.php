<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\CampaignLink;

class CalculateStatisticsJob
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    public $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle()
    {
        if ($this->campaign->sends()->count() === 0) {
            return;
        }

        $this
            ->calculateCampaignStatistics()
            ->calculateLinkStatistics();
    }

    protected function calculateCampaignStatistics()
    {
        $sendToNumberOfSubscribers = $this->campaign->sends()->count();
        $openCount = $this->campaign->opens()->count();
        $uniqueOpenCount = $this->campaign->opens()->groupBy('email_list_subscriber_id')->toBase()->getCountForPagination(['email_list_subscriber_id']);

        $openRate = round($uniqueOpenCount / $sendToNumberOfSubscribers, 2) * 100;

        $clickCount = $this->campaign->clicks()->count();
        $uniqueClickCount = $this->campaign->clicks()->groupBy('email_list_subscriber_id')->toBase()->getCountForPagination(['email_list_subscriber_id']);
        $clickRate = round($uniqueClickCount / $sendToNumberOfSubscribers, 2) * 100;

        $this->campaign->update([
            'sent_to_number_of_subscribers' => 0,
            'open_count' => $openCount,
            'unique_open_count' => $uniqueOpenCount,
            'open_rate' => $openRate,
            'click_count' => $clickCount,
            'unique_click_count' => $uniqueClickCount,
            'click_rate' => $clickRate,
        ]);

        return $this;
    }

    protected function calculateLinkStatistics()
    {
        $this->campaign->links->each(function (CampaignLink $link) {
            $link->update([
                'click_count' => $link->clicks()->count(),
                'unique_click_count' => $link->clicks()->select('email_list_subscriber_id')->groupBy('email_list_subscriber_id')->toBase()->getCountForPagination(['email_list_subscriber_id'])
            ]);

        });
    }
}


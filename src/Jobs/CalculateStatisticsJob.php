<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\EmailCampaigns\Models\Campaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Events\CampaignStatisticsCalculated;

class CalculateStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var \Spatie\EmailCampaigns\Models\Campaign */
    public $campaign;

    /** @var string */
    public $queue;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->queue = config('email-campaigns.perform_on_queue.calculate_statistics_job');
    }

    public function handle()
    {
        if ($this->campaign->sends()->count() > 0) {
            $this
                ->calculateCampaignStatistics()
                ->calculateLinkStatistics();
        }

        $this->campaign->update(['statistics_calculated_at' => now()]);

        event(new CampaignStatisticsCalculated($this->campaign));
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

        $unsubscribeCount = $this->campaign->unsubscribes()->count();
        $unsubscribeRate = round($unsubscribeCount / $sendToNumberOfSubscribers, 2) * 100;

        $this->campaign->update([
            'sent_to_number_of_subscribers' => $sendToNumberOfSubscribers,
            'open_count' => $openCount,
            'unique_open_count' => $uniqueOpenCount,
            'open_rate' => $openRate,
            'click_count' => $clickCount,
            'unique_click_count' => $uniqueClickCount,
            'click_rate' => $clickRate,
            'unsubscribe_count' => $unsubscribeCount,
            'unsubscribe_rate' => $unsubscribeRate,
        ]);

        return $this;
    }

    protected function calculateLinkStatistics()
    {
        $this->campaign->links->each(function (CampaignLink $link) {
            $link->update([
                'click_count' => $link->clicks()->count(),
                'unique_click_count' => $link->clicks()->select('email_list_subscriber_id')->groupBy('email_list_subscriber_id')->toBase()->getCountForPagination(['email_list_subscriber_id']),
            ]);
        });
    }
}

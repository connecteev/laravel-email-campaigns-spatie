<?php

namespace Spatie\EmailCampaigns\Commands;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Collection;
use PHPUnit\TextUI\Command;
use Spatie\EmailCampaigns\Jobs\RecalculateStatisticsJob;
use Spatie\EmailCampaigns\Models\Campaign;

class CalculateStatisticsCommand extends Command
{
    public $name = 'email-campaigns:calculate-statistics';

    public $description = 'Calculate the statistics of the recently sent campaigns';

    /** @var \Illuminate\Support\Carbon */
    protected $now;

    public function handle()
    {
        $this->now = now();

        collect([
            [CarbonInterval::minute(0), CarbonInterval::minute(5), CarbonInterval::minute(0)],
            [CarbonInterval::minute(0), CarbonInterval::minute(5), CarbonInterval::minute(0)],
            [CarbonInterval::minute(5), CarbonInterval::hour(2), CarbonInterval::minute(10)],
            [CarbonInterval::hour(2), CarbonInterval::day(), CarbonInterval::hour()],
            [CarbonInterval::hour(2), CarbonInterval::weeks(2), CarbonInterval::hour(4)],
        ])->each(function (array $recalculatePeriod) {
            [$startInterval, $endInterval, $recalculateThreshold] = $recalculatePeriod;

            $this
                ->findCampaignsWithStatisticsToRecalculate($startInterval, $endInterval, $recalculateThresshold)
                ->each(function (Campaign $campaign) {
                    dispatch_now(new RecalculateStatisticsJob($campaign));
                });
        });


    }

    public function calculateStatistics(
        CarbonInterval $startInterval,
        CarbonInterval $endInterval,
        CarbonInterval $recalculateThreshold
    ) {
        $periodStart = $this->now->copy()->add($startInterval);
        $periodEnd = $this->now->copy()->add($endInterval);

        $campaigns = Campaign::sentBetween($periodStart, $periodEnd)
            ->filter(function(Campaign $campaign) {
                return $campaign->statistics_calculated_at;
            });
    }
}


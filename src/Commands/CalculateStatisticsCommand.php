<?php

namespace Spatie\EmailCampaigns\Commands;

use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Jobs\CalculateStatisticsJob;

class CalculateStatisticsCommand extends Command
{
    public $name = 'email-campaigns:calculate-statistics';

    public $description = 'Calculate the statistics of the recently sent campaigns';

    /** @var \Illuminate\Support\Carbon */
    protected $now;

    public function handle()
    {
        $this->comment('Start calculating statistics...');

        $this->now = now();

        collect([
            [CarbonInterval::minute(0), CarbonInterval::minute(5), CarbonInterval::minute(0)],
            [CarbonInterval::minute(5), CarbonInterval::hour(2), CarbonInterval::minute(10)],
            [CarbonInterval::hour(2), CarbonInterval::day(), CarbonInterval::hour()],
            [CarbonInterval::day(), CarbonInterval::weeks(2), CarbonInterval::hour(4)],
        ])->each(function (array $recalculatePeriodParameters) {
            [$startInterval, $endInterval, $recalculateThreshold] = $recalculatePeriodParameters;

            $this
                ->findCampaignsWithStatisticsToRecalculate($startInterval, $endInterval, $recalculateThreshold)
                ->each(function (Campaign $campaign) {
                    $this->info("Calculating statistics for campaign id {$campaign->id}...");
                    dispatch(new CalculateStatisticsJob($campaign));
                });
        });

        $this->comment('All done!');
    }

    public function findCampaignsWithStatisticsToRecalculate(
        CarbonInterval $startInterval,
        CarbonInterval $endInterval,
        CarbonInterval $recalculateThreshold
    ): Collection {
        $periodEnd = $this->now->copy()->subtract($startInterval);
        $periodStart = $this->now->copy()->subtract($endInterval);

        return Campaign::sentBetween($periodStart, $periodEnd)
            ->get()
            ->filter(function (Campaign $campaign) use ($periodEnd, $periodStart, $recalculateThreshold) {
                if (is_null($campaign->statistics_calculated_at)) {
                    return true;
                }

                $threshold = $this->now->copy()->subtract($recalculateThreshold);

                return $campaign->statistics_calculated_at->isBefore($threshold);
            });
    }
}

<?php

namespace Spatie\EmailCampaigns\Commands;

use Illuminate\Support\Collection;
use PHPUnit\TextUI\Command;
use Spatie\EmailCampaigns\Models\Campaign;

class CalculateStatisticsCommand extends Command
{
    public $name = 'email-campaigns:calculate-statistics';

    public $description = 'Calculate the statistics of the recently sent campaigns';

    public function handle()
    {
        $now = now();

        Campaign::sentBetween($now, $now->addMinutes(5), 1);
    }

    public function calculateStatistics(Collection $campaigns, $statisticFreshnessTreshold)
    {
        return $this;
    }
}


<?php

namespace Spatie\EmailCampaigns\Commands;

use PHPUnit\TextUI\Command;

class CalculateStatisticsCommand extends Command
{
    public $name = 'email-campaigns:calculate-statistics';

    public $description = 'Calculate the statistics of the recently sent campaigns';

    public function handle()
    {

    }
}


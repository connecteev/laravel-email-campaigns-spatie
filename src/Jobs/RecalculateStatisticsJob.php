<?php

namespace Spatie\EmailCampaigns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\EmailCampaigns\Models\Campaign;

class RecalculateStatisticsJob
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

    }
}


<?php

namespace Spatie\EmailCampaigns\Tests\TestClasses;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\EmailCampaigns\Jobs\Middleware\RateLimited;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        dump('in handle');
    }

    public function middleware()
    {
        dump('middleware');

        return [new RateLimited()];
    }
}

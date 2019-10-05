<?php


namespace Spatie\EmailCampaigns\Tests\Commands;


use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Commands\CalculateStatisticsCommand;
use Spatie\EmailCampaigns\Jobs\CalculateStatisticsJob;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Tests\Factories\CampaignFactory;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\TestTime\TestTime;

class CalculateStatisticsCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Bus::fake();

        TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');

    }

    /**
     * @test
     *
     * @dataProvider caseProvider
     */
    public function it_will_recalculate_statistics_at_the_right_time(
        Carbon $sentAt,
        ?Carbon $statisticsCalculatedAt,
        bool $jobShouldHaveBeenDispatched
    )
    {
        factory(Campaign::class)->create([
            'sent_at' => $sentAt,
            'statistics_calculated_at' => $statisticsCalculatedAt,
        ]);

        $this->artisan(CalculateStatisticsCommand::class);

        $jobShouldHaveBeenDispatched
            ? Bus::assertDispatched(CalculateStatisticsJob::class)
            : Bus::assertNotDispatched(CalculateStatisticsJob::class);
    }


    public function caseProvider(): array
    {
        return [
            [now()->subMinutes(6), now()->subMinutes(6), true],
           // [now(), null, true],
           // [now()->subMinutes(5), now(), false],
        ];
    }
}

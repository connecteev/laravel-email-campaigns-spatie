<?php


namespace Spatie\EmailCampaigns\Tests\Jobs;


use Spatie\EmailCampaigns\Jobs\CalculateStatisticsJob;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Tests\TestCase;

class CalculateStatisticsJobTest extends TestCase
{
    /** @test */
    public function a_campaign_with_no_subscribers_will_get_all_zeroes()
    {
        $campaign = factory(Campaign::class)->create();

        dispatch(new CalculateStatisticsJob($campaign));

        $this->assertDatabaseHas('email_campaigns', [
            'id' => $campaign->id,
            'sent_to_number_of_subscribers' => 0,
            'open_count' => 0,
            'unique_open_count' => 0,
            'open_rate' => 0,
            'click_count' => 0,
            'unique_click_count' => 0,
            'click_rate' => 0,
        ]);
    }
}

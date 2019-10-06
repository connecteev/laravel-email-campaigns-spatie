<?php

namespace Spatie\EmailCampaigns\Tests\Jobs;

use Spatie\TestTime\TestTime;
use Illuminate\Support\Facades\Queue;
use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Jobs\CalculateStatisticsJob;
use Spatie\EmailCampaigns\Tests\Factories\CampaignFactory;

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

    /** @test */
    public function it_will_save_the_datetime_when_the_statistics_where_calculated()
    {
        TestTime::freeze();

        $campaign = factory(Campaign::class)->create();
        $this->assertNull($campaign->statistics_calculated_at);

        dispatch(new CalculateStatisticsJob($campaign));
        $this->assertEquals(now()->format('Y-m-d H:i:s'), $campaign->fresh()->statistics_calculated_at);
    }

    /** @test */
    public function it_can_calculate_statistics_regarding_opens()
    {
        $campaign = (new CampaignFactory())->withSubscriberCount(5)->create();
        dispatch(new SendCampaignJob($campaign));

        $campaignSends = $campaign->sends()->take(3)->get();
        $this
            ->simulateOpen($campaignSends)
            ->simulateOpen($campaignSends->take(1));

        dispatch(new CalculateStatisticsJob($campaign));

        $this->assertDatabaseHas('email_campaigns', [
            'id' => $campaign->id,
            'open_count' => 4,
            'unique_open_count' => 3,
            'open_rate' => 60,
        ]);
    }

    /** @test */
    public function it_can_calculate_statistics_regarding_clicks_on_the_campaign()
    {
        $campaign = (new CampaignFactory())->withSubscriberCount(5)->create([
            'html' => '<a href="https://spatie.be">Spatie</a><a href="https://flareapp.io">Flare</a><a href="https://docs.spatie.be">Docs</a>',
            'track_clicks' => true,
        ]);
        dispatch(new SendCampaignJob($campaign));

        $subscribers = $campaign->emailList->subscribers->take(3);
        $campaignLinks = $campaign->links()->take(2)->get()
            ->each(function (CampaignLink $campaignLink) use ($subscribers) {
                $this->simulateClick($campaignLink, $subscribers);
            });
        $this->simulateClick($campaignLinks->first(), $subscribers->take(1));

        dispatch_now(new CalculateStatisticsJob($campaign));

        $this->assertDatabaseHas('email_campaigns', [
            'id' => $campaign->id,
            'sent_to_number_of_subscribers' => 5,
            'click_count' => 7,
            'unique_click_count' => 3,
            'click_rate' => 60,
        ]);
    }

    /** @test */
    public function it_can_calculate_statistics_regarding_clicks_on_individual_links()
    {
        $campaign = (new CampaignFactory())->withSubscriberCount(3)->create([
            'html' => '<a href="https://spatie.be">Spatie</a>',
            'track_clicks' => true,
        ]);
        dispatch(new SendCampaignJob($campaign));

        $subscriber1 = $campaign->emailList->subscribers[0];
        $subscriber2 = $campaign->emailList->subscribers[1];
        $subscriber3 = $campaign->emailList->subscribers[2];

        $link = $campaign->links->first();

        $this
            ->simulateClick($link, $subscriber1)
            ->simulateClick($link, $subscriber2)
            ->simulateClick($link, $subscriber2);

        dispatch_now(new CalculateStatisticsJob($campaign));

        $this->assertEquals(3, $link->click_count);
        $this->assertEquals(2, $link->unique_click_count);
    }

    /** @test */
    public function the_queue_of_the_calculate_statistics_job_can_be_configured()
    {
        Queue::fake();
        config()->set('email-campaigns.perform_on_queue.calculate_statistics_job', 'custom-queue');

        $campaign = factory(Campaign::class)->create();
        dispatch(new CalculateStatisticsJob($campaign));
        Queue::assertPushed(CalculateStatisticsJob::class);
    }
}

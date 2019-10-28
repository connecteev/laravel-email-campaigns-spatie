<?php

namespace Spatie\EmailCampaigns\Tests\Actions;

use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;

class PersonalizeHtmlActionTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\CampaignSend */
    private $campaignSend;

    /** @var \Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction */
    private $personalizeHtmlAction;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaignSend = factory(CampaignSend::class)->create();

        $subscriber = $this->campaignSend->subscription->subscriber;
        $subscriber->uuid = 'my-uuid';
        $subscriber->extra_attributes = ['first_name' => 'John', 'last_name' => 'Doe'];
        $subscriber->save();

        $this->campaignSend->campaign->update(['name' => 'my campaign']);

        $this->personalizeHtmlAction = new PersonalizeHtmlAction();
    }

    /** @test */
    public function it_can_replace_an_placeholder_for_a_subscriber_attribute()
    {
        $this->assertActionResult('::subscriber.uuid::', 'my-uuid');
    }

    /** @test */
    public function it_will_not_replace_a_non_existing_attribute()
    {
        $this->assertActionResult('::subscriber.non-existing::', '::subscriber.non-existing::');
    }

    /** @test */
    public function it_can_replace_an_placeholder_for_a_subscriber_extra_attribute()
    {
        $this->assertActionResult('::subscriber.extra_attributes.first_name::', 'John');
    }

    /** @test */
    public function it_will_not_replace_an_placeholder_for_a_non_existing_subscriber_extra_attribute()
    {
        $this->assertActionResult('::subscriber.extra_attributes.non-existing::', '::subscriber.extra_attributes.non-existing::');
    }

    protected function assertActionResult(string $inputHtml, $expectedOutputHtml)
    {
        $actualOutputHtml = (new PersonalizeHtmlAction())->execute($inputHtml, $this->campaignSend);
        $this->assertEquals($expectedOutputHtml, $actualOutputHtml, "The personalize action did not produce the expected result. Expected: `{$expectedOutputHtml}`, actual: `{$actualOutputHtml}`");

        $expectedOutputHtmlWithHtmlTags = "<html>{$expectedOutputHtml}</html>";
        $actualOutputHtmlWithHtmlTags = (new PersonalizeHtmlAction())->execute("<html>{$inputHtml}</html>", $this->campaignSend);

        $this->assertEquals($expectedOutputHtmlWithHtmlTags, $actualOutputHtmlWithHtmlTags, "The personalize action did not produce the expected result when wrapped in html tags. Expected: `{$expectedOutputHtmlWithHtmlTags}`, actual: `{$actualOutputHtmlWithHtmlTags}`");
    }
}

<?php

namespace Spatie\EmailCampaigns\Tests\Features;

use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Models\Subscription;
use Spatie\EmailCampaigns\Enums\CampaignStatus;
use Spatie\EmailCampaigns\Jobs\SendCampaignJob;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Exceptions\InvalidConfig;
use Spatie\EmailCampaigns\Tests\Factories\CampaignFactory;
use Spatie\EmailCampaigns\Tests\TestClasses\CustomSubscribeAction;
use Spatie\EmailCampaigns\Tests\TestClasses\CustomPersonalizeHtmlAction;
use Spatie\EmailCampaigns\Tests\TestClasses\CustomPrepareEmailHtmlAction;
use Spatie\EmailCampaigns\Tests\TestClasses\CustomPrepareWebviewHtmlAction;
use Spatie\EmailCampaigns\Tests\TestClasses\CustomConfirmSubscriptionAction;

class CustomizableActionTest extends TestCase
{
    /** @test */
    public function the_personalize_html_action_can_be_customized()
    {
        config()->set('email-campaigns.actions.personalize_html_action', CustomPersonalizeHtmlAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_prepare_email_html_action_can_be_customized()
    {
        config()->set('email-campaigns.actions.prepare_email_html_action', CustomPrepareEmailHtmlAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_prepare_webview_html_action_can_be_customized()
    {
        config()->set('email-campaigns.actions.prepare_webview_html_action', CustomPrepareWebviewHtmlAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_subscribe_action_can_be_customized()
    {
        config()->set('email-campaigns.actions.subscribe_action', CustomSubscribeAction::class);

        /** @var \Spatie\EmailCampaigns\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create();

        $subscription = $emailList->subscribe('john@example.com');

        $this->assertEquals('overridden@example.com', $subscription->subscriber->email);
    }

    /** @test */
    public function the_confirm_subscription_class_can_be_customized()
    {
        config()->set('email-campaigns.actions.confirm_subscription_action', CustomConfirmSubscriptionAction::class);

        /** @var \Spatie\EmailCampaigns\Models\Subscription $subscription */
        $subscription = factory(Subscription::class)->create([
            'status' => SubscriptionStatus::PENDING,
        ]);

        $subscription->confirm();

        $this->assertEquals('overridden@example.com', $subscription->subscriber->email);
    }

    /** @test */
    public function a_wrongly_configured_class_will_result_in_an_exception()
    {
        config()->set('email-campaigns.actions.subscribe_action', 'invalid-class');

        /** @var \Spatie\EmailCampaigns\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create();

        $this->expectException(InvalidConfig::class);

        $emailList->subscribe('john@example.com');
    }
}

<?php

namespace Spatie\EmailCampaigns\Tests\Rules;

use Spatie\EmailCampaigns\Tests\TestCase;
use Spatie\EmailCampaigns\Models\EmailList;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Rules\EmailListSubscriptionRule;

class EmailRuleSubscriptionTest extends TestCase
{
    /** @var \Spatie\EmailCampaigns\Models\EmailList */
    private $emailList;

    /** @var \Spatie\EmailCampaigns\Rules\EmailListSubscriptionRule */
    private $rule;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = factory(EmailList::class)->create();

        $this->rule = new EmailListSubscriptionRule($this->emailList);
    }

    /** @test */
    public function it_will_not_pass_if_the_given_email_is_already_subscribed()
    {
        $this->assertTrue($this->rule->passes('email', 'john@example.com'));
        $this->emailList->subscribe('john@example.com');
        $this->assertFalse($this->rule->passes('email', 'john@example.com'));

        $otherEmailList = factory(EmailList::class)->create();
        $rule = new EmailListSubscriptionRule($otherEmailList);
        $this->assertTrue($rule->passes('email', 'john@example.com'));
    }

    /** @test */
    public function it_will_pass_for_emails_that_are_still_pending()
    {
        $this->emailList->update(['requires_double_opt_in' => true]);
        $this->emailList->subscribe('john@example.com');
        $this->assertEquals(SubscriptionStatus::PENDING, $this->emailList->getSubscriptionStatus('john@example.com'));

        $this->assertTrue($this->rule->passes('email', 'john@example.com'));
    }

    /** @test */
    public function it_will_pass_for_emails_that_are_unsubscribed()
    {
        $this->emailList->update(['requires_double_opt_in' => true]);
        $this->emailList->subscribe('john@example.com');
        $this->emailList->unsubscribe('john@example.com');
        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $this->emailList->getSubscriptionStatus('john@example.com'));

        $this->assertTrue($this->rule->passes('email', 'john@example.com'));
    }
}

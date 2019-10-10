<?php

namespace Spatie\EmailCampaigns\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\EmailCampaigns\Enums\SubscriptionStatus;
use Spatie\EmailCampaigns\Models\EmailList;

class EmailListSubscriptionRule implements Rule
{
    /** @var \Spatie\EmailCampaigns\Models\EmailList */
    protected $emailList;

    /** @var string */
    protected $attribute;

    public function __construct(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;

        return $this->emailList->getSubscriptionStatus($value) !== SubscriptionStatus::SUBSCRIBED;
    }

    public function message()
    {
        return __('email-campaigns::messages.email_list_email', [
            'attribute' => $this->attribute,
        ]);
}}

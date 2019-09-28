<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Models\EmailCampaign;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;

class PersonalizeHtmlAction
{
    public function handle($html, EmailListSubscriber $emailSubscriber, EmailCampaign $emailCampaign)
    {
        $html = str_replace(urlencode('[[subscriberUuid]]'), $emailSubscriber->uuid, $html);

        return $html;
    }
}

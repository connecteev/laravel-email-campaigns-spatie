<?php

namespace Spatie\EmailCampaigns\Tests\TestClasses;

use Spatie\EmailCampaigns\Models\CampaignSend;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;

class CustomPersonalizeHtmlAction extends PersonalizeHtmlAction
{
    public function execute($html, CampaignSend $pendingSend)
    {
        $pendingSend->subscription->subscriber->update([
            'email' => 'overridden@example.com',
        ]);

        return parent::execute($html, $pendingSend);
    }
}

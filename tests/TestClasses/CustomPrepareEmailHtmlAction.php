<?php

namespace Spatie\EmailCampaigns\Tests\TestClasses;

use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;
use Spatie\EmailCampaigns\Models\Campaign;

class CustomPrepareEmailHtmlAction extends PrepareEmailHtmlAction
{
    public function execute(Campaign $campaign)
    {
        $campaign->emailList->subscribers->first()->update(['email' => 'overridden@example.com']);

        parent::execute($campaign);
    }
}

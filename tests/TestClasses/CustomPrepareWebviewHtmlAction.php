<?php

namespace Spatie\EmailCampaigns\Tests\TestClasses;

use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Actions\PrepareWebviewHtmlAction;

class CustomPrepareWebviewHtmlAction extends PrepareWebviewHtmlAction
{
    public function execute(Campaign $campaign)
    {
        $campaign->emailList->subscribers->first()->update(['email' => 'overridden@example.com']);

        parent::execute($campaign);
    }
}

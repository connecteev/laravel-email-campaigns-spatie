<?php

namespace Spatie\EmailCampaigns\Actions;

use Spatie\EmailCampaigns\Models\Campaign;

class PrepareWebviewHtmlAction
{
    public function execute(Campaign $campaign)
    {
        $campaign->webview_html = $campaign->html;
        $campaign->save();
    }
}

<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Spatie\EmailCampaigns\Models\Campaign;
use Spatie\EmailCampaigns\Enums\CampaignStatus;

class CampaignWebviewController
{
    public function __invoke(string $campaignUuid)
    {
        if (! $campaign = Campaign::findByUuid($campaignUuid)) {
            abort(404);
        }

        if ($campaign->status === CampaignStatus::CREATED) {
            abort(404);
        }

        return view('email-campaigns::campaign.webview', compact('campaign'));
    }
}

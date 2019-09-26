<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\EmailCampaigns\Jobs\RegisterClickJob;

class TrackClicksController
{
    public function __invoke(Request $request, string $campaignLinkUuid, string $subscriberUuid = null)
    {
        if (! is_null($subscriberUuid)) {
            dispatch(new RegisterClickJob($campaignLinkUuid, $subscriberUuid));
        }

        $redirectUrl = $request->input('redirect');

        return redirect()->to($redirectUrl);
    }
}

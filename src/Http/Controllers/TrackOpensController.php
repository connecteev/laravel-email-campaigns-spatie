<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Spatie\EmailCampaigns\Support\WebBeacon;
use Spatie\EmailCampaigns\Jobs\RegisterOpenJob;

class TrackOpensController
{
    public function __invoke(string $campaignSendUuid)
    {
        if (! is_null($campaignSendUuid)) {
            dispatch(new RegisterOpenJob($campaignSendUuid));
        }

        $webBeaconContent = WebBeacon::content();

        return response($webBeaconContent)->withHeaders([
            'Content-type' => 'image/gif',
            'Content-Length' => strlen($webBeaconContent),
            'Cache-Control' => 'private, no-cache, no-cache=Set-Cookie, proxy-revalidate',
            'Expires' => 'Wed, 11 Jan 2000 12:59:00 GMT',
            'Last-Modified' => 'Wed, 11 Jan 2006 12:59:00 GMT',
            'Pragma' => 'no-cache',
        ]);
    }
}

<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Spatie\EmailCampaigns\Jobs\RegisterOpenJob;

class TrackOpensController
{
    public function __invoke(string $campaignSendUuid)
    {
        if (! is_null($campaignSendUuid)) {
            dispatch(new RegisterOpenJob($campaignSendUuid));
        }

        $webBeaconContent = sprintf('%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%c%', 71, 73, 70, 56, 57, 97, 1, 0, 1, 0, 128, 255, 0, 192, 192, 192, 0, 0, 0, 33, 249, 4, 1, 0, 0, 0, 0, 44, 0, 0, 0, 0, 1, 0, 1, 0, 0, 2, 2, 68, 1, 0, 59);

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

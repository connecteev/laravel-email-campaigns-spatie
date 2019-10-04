<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Testing\File;
use Spatie\EmailCampaigns\Jobs\RegisterClickJob;
use Spatie\EmailCampaigns\Jobs\RegisterOpenJob;
use Spatie\EmailCampaigns\Support\Image;

class TrackOpensController
{
    public function __invoke(Request $request, string $campaignSendUuid)
    {
        if (! is_null($campaignSendUuid)) {
            dispatch(new RegisterOpenJob($campaignSendUuid));
        }

        $webBeaconContent  = file_get_contents(__DIR__ . '/../../../resources/images/beacon.png');

        return response($webBeaconContent)->header('Content-type','image/png');
    }
}

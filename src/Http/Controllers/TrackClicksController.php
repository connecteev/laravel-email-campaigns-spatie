<?php

namespace Spatie\EmailCampaigns\Http\Controllers;

use Spatie\EmailCampaigns\Models\CampaignLink;
use Spatie\EmailCampaigns\Models\EmailListSubscriber;

class TrackClicksController
{
    public function __invoke(CampaignLink $link, EmailListSubscriber $subscriber = null)
    {
        if ($subscriber) {
            $link->registerClick($subscriber);
        }

        return redirect()->to($link->original_link);
    }
}


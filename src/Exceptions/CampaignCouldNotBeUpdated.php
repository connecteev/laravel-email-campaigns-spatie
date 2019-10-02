<?php

namespace Spatie\EmailCampaigns\Exceptions;

use Exception;
use Spatie\EmailCampaigns\Models\Campaign;

class CampaignCouldNotBeUpdated extends Exception
{
    public static function beingSent(Campaign $campaign): self
    {
        return new static("The campaign `{$campaign->name}` cannot be updated because it is being sent.");
    }

    public static function alreadySent(Campaign $campaign): self
    {
        return new static("The campaign `{$campaign->name}` cannot be updated because it was already sent.");
    }
}

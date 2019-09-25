<?php

namespace Spatie\EmailCampaigns\Exceptions;

use Exception;
use Spatie\EmailCampaigns\Models\EmailCampaign;

class CampaignCouldNotBeUpdated extends Exception
{
    public static function beingSent(EmailCampaign $emailCampaign): self
    {
        return new static("The campaign `{$emailCampaign->name}` cannot be updated because it is being sent.");
    }

    public static function alreadySent(EmailCampaign $emailCampaign): self
    {
        return new static("The campaign `{$emailCampaign->name}` cannot be updated because it was already sent.");
    }
}


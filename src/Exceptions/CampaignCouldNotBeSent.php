<?php

namespace Spatie\EmailCampaigns\Exceptions;

use Spatie\EmailCampaigns\Models\EmailCampaign;

class CampaignCouldNotBeSent extends \Exception
{
    public static function beingSent(EmailCampaign $emailCampaign): self
    {
        return new static("The campaign `{$emailCampaign->name}` can't be sent, because it is already being sent.");
    }

    public static function alreadySent(EmailCampaign $emailCampaign): self
    {
        return new static("The campaign `{$emailCampaign->name}` can't be sent, because it was already sent.");
    }

    public static function noListSet(EmailCampaign $emailCampaign)
    {
        return new static("The campaign `{$emailCampaign->name}` can't be sent, because there is no list set to send it to.");
    }
}


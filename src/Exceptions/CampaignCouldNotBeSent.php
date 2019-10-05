<?php

namespace Spatie\EmailCampaigns\Exceptions;

use Exception;
use ErrorException;
use Spatie\EmailCampaigns\Models\Campaign;

class CampaignCouldNotBeSent extends Exception
{
    public static function beingSent(Campaign $campaign): self
    {
        return new static("The campaign `{$campaign->name}` can't be sent, because it is already being sent.");
    }

    public static function alreadySent(Campaign $campaign): self
    {
        return new static("The campaign `{$campaign->name}` can't be sent, because it was already sent.");
    }

    public static function noListSet(Campaign $campaign)
    {
        return new static("The campaign `{$campaign->name}` can't be sent, because there is no list set to send it to.");
    }

    public static function noSubjectSet(Campaign $campaign)
    {
        return new static("The campaign `{$campaign->name}` can't be sent, because no subject has been set.");
    }

    public static function noContent(Campaign $campaign): self
    {
        return new static("The campaign `{$campaign->name}` can't be sent because not content has been set.");
    }

    public static function invalidContent(Campaign $campaign, ErrorException $errorException): self
    {
        return new static("The campaign `{$campaign->name}` can't be sent because the content isn't valid. Please check if the html is valid. DOMDocument reported: `{$errorException->getMessage()}`", 0, $errorException);
    }
}

<?php

namespace Spatie\EmailCampaigns\Exceptions;

use Exception;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;

class InvalidConfig extends Exception
{
    public static function invalidPrepareEmailAction(string $currentValue)
    {
        $expectedClass = PrepareEmailHtmlAction::class;

        return new static("The class currently specified in the `email-campaigns.actions.prepare_email` key '{$currentValue}' should be or extend `{$expectedClass}`.");
    }

    public static function invalidPersonalizeHtmlAction(string $currentValue): self
    {
        $expectedClass = PersonalizeHtmlAction::class;

        return new static("The class specified in the `email-campaigns.actions.personalize_html` key '{$currentValue}' should be or extend `{$expectedClass}`.");
    }
}


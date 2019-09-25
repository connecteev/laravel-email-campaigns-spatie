<?php

namespace Spatie\EmailCampaigns\Exceptions;

use Exception;
use Spatie\EmailCampaigns\Actions\PersonalizeHtmlAction;
use Spatie\EmailCampaigns\Actions\PrepareEmailHtmlAction;

class InvalidConfig extends Exception
{
    public static function invalidPrepareEmailAction()
    {
        $expectedClass = PrepareEmailHtmlAction::class;

        return new static("The class specified in the `email-campaigns.actions.prepare_email` key should extend or be `{$expectedClass}`");
    }

    public static function invalidPersonalizeHtmlAction(): self
    {
        $expectedClass = PersonalizeHtmlAction::class;

        return new static("The class specified in the `email-campaigns.actions.personalize_html` key should extend or be `{$expectedClass}`");
    }
}


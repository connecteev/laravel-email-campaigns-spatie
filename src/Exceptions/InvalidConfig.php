<?php

namespace Spatie\EmailCampaigns\Exceptions;

use Exception;

class InvalidConfig extends Exception
{
    public static function invalidAction(string $actionName, string $configuredClass, string $actionClass)
    {
        return new static("The class currently specified in the `email-campaigns.actions.{$actionName}` key '{$configuredClass}' should be or extend `{$actionClass}`.");
    }
}

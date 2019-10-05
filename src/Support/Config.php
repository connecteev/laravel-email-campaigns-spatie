<?php

namespace Spatie\EmailCampaigns\Support;

use Spatie\EmailCampaigns\Actions\SubscribeAction;
use Spatie\EmailCampaigns\Exceptions\InvalidConfig;

class Config
{
    public static function getActionClass(string $actionName, string $actionClass): object
    {
        $configuredClass = config("email-campaigns.actions.{$actionName}");

        if (!is_a($configuredClass, $actionClass, true)) {
            throw InvalidConfig::invalidAction($actionName, $configuredClass ?? '', $actionClass);
        }

        return app($configuredClass);
    }
}

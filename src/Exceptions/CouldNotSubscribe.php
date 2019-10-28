<?php

namespace Spatie\EmailCampaigns\Exceptions;

use Exception;

class CouldNotSubscribe extends Exception
{
    public static function invalidEmail(string $email)
    {
        return new static("Could not subscribe `{$email}` because it isn't a valid e-mail");
    }
}

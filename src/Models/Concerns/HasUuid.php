<?php

namespace Spatie\EmailCampaigns\Models\Concerns;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasUuid
{
    public static function bootHasUuid()
    {
        static::creating(function (Model $model) {
            $model->uuid = (string)Str::uuid();
        });
    }

    public static function findByUuid(string $uuid): ?Model
    {
        return static::where('uuid', $uuid)->first();
    }
}

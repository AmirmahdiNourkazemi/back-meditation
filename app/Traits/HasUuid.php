<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUUID(): void
    {
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }
}

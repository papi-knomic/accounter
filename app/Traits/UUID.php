<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UUID {


    protected static function boot() {
        // Boot other traits on the Model
        parent::boot();

        /**
         * Listen for the creating event on the user model to set the uid.
         */
        static::creating(function ($model) {
            if (is_null($model->uuid)) {
                $model->setAttribute('uuid', Str::uuid()->toString());
            }
        });
    }

}

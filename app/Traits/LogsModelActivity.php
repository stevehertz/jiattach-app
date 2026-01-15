<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait LogsModelActivity{
    public static function bootLogsModelActivity()
    {
        static::created(function (Model $model) {
            activity_log(
                class_basename($model).' created',
                'created',
                ['id' => $model->id],
                strtolower(class_basename($model))
            );
        });

        static::updated(function (Model $model) {
            activity_log(
                class_basename($model).' updated',
                'updated',
                [
                    'id' => $model->id,
                    'changes' => $model->getChanges(),
                ],
                strtolower(class_basename($model))
            );
        });

        static::deleted(function (Model $model) {
            activity_log(
                class_basename($model).' deleted',
                'deleted',
                ['id' => $model->id],
                strtolower(class_basename($model))
            );
        });
    }
}
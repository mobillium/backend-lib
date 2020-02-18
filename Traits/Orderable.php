<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait Orderable
{
    public static function bootOrderable()
    {
        static::addGlobalScope(new OrderableScope());

        self::created(function (Model $model) {
            $model->order = $model->id;
            $model->save();
        });
    }

    public function getOrderedByColumn()
    {
        return defined('static::ORDERED_BY') ? static::ORDERED_BY : 'order';
    }

    public function getQualifiedOrderedByColumn()
    {
        return $this->qualifyColumn($this->getOrderedByColumn());
    }
}
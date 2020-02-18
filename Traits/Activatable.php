<?php

namespace App\Traits;

trait Activatable
{
    public static function bootActivatable()
    {
        static::addGlobalScope(new ActivatableScope());
    }

    public function getActivatableColumn()
    {
        return defined('static::ACTIVATABLE') ? static::ACTIVATABLE : 'is_active';
    }

    public function getQualifiedActivatableColumn()
    {
        return $this->qualifyColumn($this->getActivatableColumn());
    }
}
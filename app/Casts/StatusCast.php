<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class StatusCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function get($model, $key, $value, $attributes)
    {
        if ($attributes['status'] == 0)
            return 'lost';
        elseif ($attributes['status'] == 1)
            return 'unresolved';
        elseif ($attributes['status'] == 2)
            return 'won';
        else
            return 'N/A';
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param string $value
     * @param array $attributes
     * @return int
     */
    public function set($model, $key, $value, $attributes)
    {
        if (!strcmp($value, 'lost')) {
            $attributes['status'] = 0;
            return 0;
        } elseif (!strcmp($value, 'unresolved')) {
            $attributes['status'] = 1;
            return 1;
        } elseif (!strcmp($value, 'won')) {
            $attributes['status'] = 2;
            return 2;
        } else
            return -1; // fallback
    }
}

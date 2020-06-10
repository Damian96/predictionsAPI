<?php

namespace App\Casts;

use App\Objects\PredictionObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class PredictionCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param string $value
     * @param array $attributes
     * @return string
     */
    public function get($model, $key, $value, $attributes)
    {
        $chars = str_split($value, 1);

        if (count($chars) == 1) {
            $market_type = '1x2';
            if ($chars[0] == '1') {
                $result = 'H';
            } else if ($chars[0] == '2') {
                $result = 'A';
            } else {
                $result = 'X';
            }
            return (new PredictionObject($market_type, $result))->toString();
        } else {
            $market_type = 'correct_score';
            $score = $chars[0] . ':' . $chars[2];
            return (new PredictionObject($market_type, $score))->toString();
        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param string $value
     * @param array $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return $value;
    }
}

<?php


namespace App\Objects;


use App\Casts\PredictionCast;
use Illuminate\Contracts\Database\Eloquent\Castable;

/**
 * Class PredictionObject
 * @property String $market_type
 * @property String $result
 * @property String $score
 * @package App\Objects
 */
class PredictionObject implements Castable
{
    private $market_type;
    private $result;
    private $score;

    /**
     * PredictionObject constructor.
     * @param string $market_type
     * @param mixed $result
     */
    public function __construct($market_type, $result)
    {
        $this->market_type = $market_type;
        $this->result = $result;
        if ($this->market_type === 'correct_score') {
            $this->score = $result;
        } else {
            $this->result = $result;
        }
    }

    /**
     * @inheritDoc
     */
    public static function castUsing()
    {
        return PredictionCast::class;
    }

    /**
     * Transforms this class to a String
     * @return String
     */
    public function toString()
    {
        return $this->market_type === 'correct_score' ? $this->score : $this->result;
    }

    /**
     * @return String
     */
    public function getMarketType(): string
    {
        return $this->market_type;
    }

    /**
     * @return String
     */
    public function getWinner(): string
    {
        return $this->result;
    }

    /**
     * @return String
     */
    public function getScore(): string
    {
        return $this->score;
    }
}

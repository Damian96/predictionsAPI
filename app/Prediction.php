<?php

namespace App;

use App\Casts\PredictionCast;
use App\Casts\StatusCast;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int prediction
 * @property string market_type
 * @property int status
 * @method static whereId(int $id)
 */
class Prediction extends Model
{
    protected $table = 'predictions';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $connection = 'mysql';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'event_id' => 'int',
        'prediction' => PredictionCast::class,
        'status' => StatusCast::class,
//        'market_type' => MarketType::class,
        'market_type' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'market_type' => '0',
        'status' => '0',
        'created_at' => null,
        'updated_at' => null,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id', 'market_type', 'prediction', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        switch ($key) {
            case 'prediction':
                return $this->getAttribute('prediction')->toString();
            default:
                return parent::__get($key);
        }
    }
}

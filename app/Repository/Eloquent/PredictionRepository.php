<?php


namespace App\Repository\Eloquent;


use App\Prediction;
use App\Repository\PredictionRepositoryInterface;
use Illuminate\Support\Collection;
use Throwable;

class PredictionRepository extends BaseRepository implements PredictionRepositoryInterface
{

    /**
     * PredictionRepository constructor.
     * @param Prediction $model
     */
    public function __construct(Prediction $model)
    {
        parent::__construct($model);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection
    {
        return Prediction::all()->collect();
    }

    /**
     * @param int $id
     * @return Prediction|bool
     * @throws \Throwable
     */
    public function find($id): Prediction
    {
        try {
            return Prediction::whereId($id)->findOrFail();
        } catch (Throwable $e) {
            throw_if(env('APP_DEBUG', false), $e);
            return false;
        }
    }
}

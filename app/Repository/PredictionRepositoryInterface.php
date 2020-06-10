<?php


namespace App\Repository;


use App\Prediction;
use Illuminate\Support\Collection;

interface PredictionRepositoryInterface
{
    /**
     * @return Collection
     */
    public function all(): Collection;

    /**
     * @param int $id
     * @return Prediction
     */
    public function find($id): Prediction;
}

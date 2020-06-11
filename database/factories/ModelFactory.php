<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$markets = ['1x2', 'correct_score'];
$predictions = ['H', 'A', 'X', '1:0', '0:0', '0:1'];
$statuses = ['lost', 'unresolved', 'won'];
$ids = range(1, 10);

$factory->define(\App\Prediction::class, function () use ($markets, $predictions, $statuses, $ids) {
    $now = \Carbon\Carbon::now(config('app.timezone'));
    return [
        'id' => array_pop($ids),
        'event_id' => rand(1, 10),
        'market_type' => $markets[array_rand($markets, 1)],
        'prediction' => $predictions[array_rand($predictions, 1)],
        'status' => $statuses[array_rand($statuses, 1)],
        'created_at' => $now->timestamp,
        'updated_at' => $now->timestamp
    ];
});

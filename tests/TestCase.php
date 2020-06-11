<?php

namespace Tests;

use App\Prediction;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * The callbacks that should be run before the application is destroyed.
     *
     * @var array
     */
    protected $beforeApplicationDestroyedCallbacks = [];

    /**
     * The callbacks that should be run after the application is created.
     *
     * @var array
     */
    protected $afterApplicationCreatedCallbacks = [];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        array_push($this->beforeApplicationDestroyedCallbacks, array(self::class, 'deleteTransactions'));
        array_push($this->afterApplicationCreatedCallbacks, array(self::class, 'insertTestModels'));
    }

    /**
     * Delete all transactions made during the test.
     *
     * @return void
     * @throws \Throwable
     */
    public static function deleteTransactions()
    {
        try {
            Prediction::query()->truncate();
        } catch (\Throwable $e) {
            throw_if(env('APP_DEBUG', false), $e);
        }
    }

    /**
     * Inserts \App\Prediction test (DUMMY) models, into the database.
     *
     * @return void
     * @throws \Throwable
     */
    public static function insertTestModels()
    {
        $prediction = factory(\App\Prediction::class)->make();

        try {
            $prediction->setAttribute('id', null);
            $prediction->setAttribute('event_id', \Tests\Unit\PredictionsTest::DUMMY_EVENT);
            $prediction->saveOrFail();
        } catch (\Throwable $e) {
            throw_if(env('APP_DEBUG', false), $e);
        }
    }
}

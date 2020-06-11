<?php

namespace Tests\Unit;


/**
 * Tests the successful cases of the Prediction API.
 * Class PredictionsTest
 * @package Tests\Unit
 *
 * @property int DUMMY_EVENT The test Model's `event_id` attribute
 * @property int DUMMY_ID The test Model's `id` attribute
 */
class PredictionsTest extends \Tests\TestCase
{
    const DUMMY_EVENT = 333;
    const DUMMY_ID = 1;

    /**
     * Tests the successful retrieval of all Predictions.
     *
     * @return void
     */
    public function testGetAll()
    {
        $response = $this->get('/v1/predictions', [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertDontSeeText('error');
    }

    /**
     * Tests the successful creation of a Prediction.
     *
     * @return void
     */
    public function testSuccessfulCreate()
    {
        $response = $this->post('/v1/predictions/', [
            'event_id' => rand(1, 10),
            'market_type' => 'correct_score',
            'prediction' => '3:2'
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertDontSeeText('error');
    }

    /**
     * Tests the successful update of a Prediction.
     *
     * @return void
     */
    public function testSuccessfulUpdate()
    {
        $id = self::DUMMY_ID;
        $response = $this->post("/v1/predictions/{$id}/status", [
            'status' => 'lost'
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertDontSeeText('error');
    }
}

<?php

namespace Tests\Unit;


use App\Http\Controllers\ApiController;

/**
 * Tests the successful cases of the Prediction API.
 * Class FailedPredictionsTest
 * @package Tests\Unit
 */
class FailedPredictionsTest extends \Tests\TestCase
{
    /**
     * Tests the unsuccessful creation of a Prediction.
     *
     * @return void
     */
    public function testUnsuccessfulCreate()
    {
        $response = $this->post('/v1/predictions/', [
            'event_id' => rand(1, 10),
            'market_type' => 'loremipsum',
            'prediction' => '5:10'
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(ApiController::BAD_REQUEST);
        $response->assertSeeText('error');
    }

    /**
     * Tests the unsuccessful update of a Prediction.
     *
     * @return void
     */
    public function testUnsuccessfulUpdate()
    {
        $id = PredictionsTest::DUMMY_ID;
        $response = $this->post("/v1/predictions/{$id}/status", [
            'status' => 'loremipsum'
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(ApiController::BAD_REQUEST);
        $response->assertSeeText('error');
    }
}

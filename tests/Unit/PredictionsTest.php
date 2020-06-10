<?php


class PredictionsTest extends \Tests\TestCase
{
    /**
     * @var int DUMMY_EVENT
     */
    const DUMMY_EVENT = 333;

    public function testGetAll()
    {
        $response = $this->get('/v1/predictions', [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertDontSeeText('error');
    }

    public function testSuccessfulCreate()
    {
        $response = $this->post('/v1/predictions/', [
            'event_id' => self::DUMMY_EVENT,
            'market_type' => 'correct_score',
            'prediction' => '3:2'
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertDontSeeText('error');
    }

    /**
     * Tests the successfull update of a Prediction
     */
    public function testSuccessfulUpdate()
    {
        $id = \App\Prediction::all()->first()->id;
        $response = $this->post("/v1/predictions/{$id}/status", [
            'status' => 'lost'
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertDontSeeText('error');
    }
}

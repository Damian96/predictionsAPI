<?php

namespace App\Http\Controllers;

use App\Http\Resources\PredictionCollection;
use App\Prediction;
use http\Exception\RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{

    public function __construct()
    {
        $this->middleware('api');
    }

    /**
     * @param string $action
     * @return array
     */
    public function rules(string $action)
    {
        switch ($action) {
            case 'updateStatus':
                return [
                    'status' => 'required|string|in:lost,unresolved,won',
                ];
            case 'create':
                return [
                    'event_id' => 'required|int',
                    'market_type' => 'required|string|in:1x2,correct_score',
                    'prediction' => 'required|regex:/((H|A|X)|\d:\d)/i',
                ];
            default:
                return [];
        }
    }


    /**
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $collection = new PredictionCollection(Prediction::all());

        return $this->sendResponse($collection);
    }

    /**
     * @param Prediction $prediction
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function updateStatus(Prediction $prediction, Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(__FUNCTION__));

        if (!$validator->fails()) {
            if ($prediction->update(['status' => $request->get('status', 'unresolved')])) {
                return $this->sendResponse($prediction->toJson());
            } else {
                throw_if(env('APP_DEBUG', false), new RuntimeException(sprintf('Could not update Prediction with ID [%d]', $prediction->id)));
                return $this->sendResponse(['error' => sprintf('Could not update Prediction with ID [%d]', $prediction->id)], 503);
            }
        }

        return $this->sendResponse(['error' => $validator->errors()->first('status')], 503);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(__FUNCTION__));

        if (!$validator->fails()) {
            $prediction = new Prediction($request->all());

            try {
                $prediction->saveOrFail();
            } catch (\Throwable $exception) {
                throw_if(env('APP_DEBUG', false), new RuntimeException(sprintf('Could not create Prediction'), 503));
                return $this->sendResponse($prediction->toJson());
            }
        }

        return $this->sendResponse(['error' => $validator->errors()->first()], 503);
    }

    /**
     * @param mixed $data
     * @param int $code
     * @return \Illuminate\Http\Response
     */
    private function sendResponse($data, $code = 200)
    {
        try {
            $json = json_encode($data);
            return \response($json, $code, [
                'Content-Type' => 'application/json'
            ]);
        } catch (\Exception $e) {
            return \response(json_encode(['message' => $e->getMessage(), 'code' => $e->getCode(), 'trace' => $e->getTraceAsString()]), $code, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

}

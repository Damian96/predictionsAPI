<?php

namespace App\Http\Controllers;

use App\Prediction;
use App\Repository\Eloquent\PredictionRepository;
use http\Exception\RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ApiController extends Controller
{

    /**
     * @var PredictionRepository
     */
    private PredictionRepository $predictionService;

    public function __construct(PredictionRepository $service)
    {
        $this->predictionService = $service;
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
                    'event_id' => 'required|int|min:1',
                    'market_type' => 'required|string|in:1x2,correct_score',
                    'prediction' => [
                        'required',
                        'string',
                        'regex:/(H|A|X|\d:\d)/i'
                    ]
                ];
            default:
                return [];
        }
    }

    /**
     * @param string $action
     * @return array
     */
    public function messages($action)
    {
        switch ($action) {
            // @TODO:
            case 'create':
                return [

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
        return $this->sendResponse($this->predictionService->all());
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
        $validator = Validator::make($request->all(), $this->rules(__FUNCTION__), $this->messages(__FUNCTION__));

        if (!$validator->fails()) {
            $prediction = new Prediction($request->all());

            try {
                $prediction->saveOrFail();
                return $this->sendResponse($prediction->toJson());
            } catch (Throwable $exception) {
                throw_if(env('APP_DEBUG', false), new RuntimeException(sprintf('Could not create Prediction'), 503));
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
        if (is_string($data)) {
            return \response($data, $code, [
                'Content-Type' => 'application/json'
            ]);
        }
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

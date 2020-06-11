<?php

namespace App\Http\Controllers;

use App\Prediction;
use App\Repository\Eloquent\PredictionRepository;
use http\Exception\RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    const BAD_REQUEST = 400;
    const NOT_FOUND = 404;

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
            case 'updateStatus':
                return [
                    'status.required' => 'The selected status is required!',
                    'status.string' => 'The selected status should be a string!',
                    'status.in' => 'The selected status is invalid!'
                ];
            case 'create':
                return [
                    'event_id.required' => 'The selected event_id is required!',
                    'event_id.int' => 'The selected event_id should be an integer!',

                    'market_type.required' => 'The selected market_type is required!',
                    'market_type.string' => 'The selected market_type should be a string!',
                    'market_type.in' => 'The selected market_type is invalid!',

                    'prediction.required' => 'The selected prediction is required!',
                    'prediction.string' => 'The selected prediction should be a string!',
                    'prediction.regex' => 'The selected prediction is invalid!'
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
        $validator = Validator::make($request->all(), $this->rules(__FUNCTION__), $this->messages(__FUNCTION__));

        if (!$validator->fails()) {
            if ($prediction->update(['status' => $request->get('status', 'unresolved')])) {
                return $this->sendResponse($prediction->toJson());
            } else {
                throw_if(env('APP_DEBUG', false), new RuntimeException(sprintf('Could not update Prediction with ID [%d]', $prediction->id)));
                return $this->sendResponse(['error' => sprintf('Could not update Prediction with ID [%d]', $prediction->id)], 503);
            }
        }

        Log::error(sprintf("Client:[%s] failed to %s:%s, [%s].", __CLASS__, __FUNCTION__, $request->getClientIp(), $validator->errors()->first('status')));
        return $this->sendResponse(['error' => $validator->errors()->first('status')], self::BAD_REQUEST);
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

        Log::error(sprintf("Client:[%s] failed to %s:%s, [%s].", __CLASS__, __FUNCTION__, $request->getClientIp(), $validator->errors()->first()));
        return $this->sendResponse(['error' => $validator->errors()->first()], self::BAD_REQUEST);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\Response
     */
    public function error400($message = 'The server could not understand the request due to invalid syntax.')
    {
        return $this->sendResponse(['error' => $message], self::BAD_REQUEST);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\Response
     */
    public function error404($message = 'Prediction not found.')
    {
        return $this->sendResponse(['error' => $message], self::NOT_FOUND);
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

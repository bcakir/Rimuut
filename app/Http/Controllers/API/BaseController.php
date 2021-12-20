<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * Success response
     * @param $result
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($result, $message, $status = 200)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    /**
     * Error response
     * @return \Illuminate\Http\Response
     */
    protected function sendError($error, $errorMessages = [], $status = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorMessages))
        {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $status);
    }
}
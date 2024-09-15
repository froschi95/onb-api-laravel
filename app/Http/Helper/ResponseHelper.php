<?php

namespace App\Http\Helper;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Common function to display success - JSON Response
     * @param string $status
     * @param string $message
     * @param array $data
     * @param string $statusCode
     * @return JsonResponse
     */
    public static function success($status = 'success', $message = null, $data = [], $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Common function to display error - JSON Response
     * @param string $status
     * @param string $message
     * @param string $statusCode
     * @return JsonResponse
     */
    public static function error($status = "error", $message = null, $statusCode = 400)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
        ], $statusCode);
    }
}
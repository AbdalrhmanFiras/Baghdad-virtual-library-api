<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function responseError($payload = null, string $message = 'error', int $status = 422, $header = [])
    {
        return response()->json([
            'message' => $message,
            'payload' => $payload,
        ], $status, $header);
    }

    public function responseSuccess($payload = null, string $message = 'success', int $status = 200, $header = [])
    {
        return response()->json([
            'message' => $message,
            'payload' => $payload,
        ], $status, $header);
    }
}

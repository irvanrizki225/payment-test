<?php

namespace App\Helpers;

class ResponseFormatter
{

    protected static $response = [
    'meta' => [
        'message' => null,
        'status' => 'success',
        'code' => 200,
    ],
    'data' => null
    ];

    public static function success($data=null,$message=null)
    {
        self::$response['meta']['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    public static function error($data=null, $message=null, $code=null)
    {
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

}
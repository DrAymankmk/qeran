<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

/**
 * this class will have all app response code with message body
 */
class RespondActive
{

    /**
     * Custom response code, message and data
     * @param int $code
     * @param string $message
     * @param            $data
     * @return  JsonResponse
     */
    public static function customResponse(int $code, $message = null, $data = null)
    {
        return response()->json([
            'code' => $code,
            'message' => __($message),
            'data' => $data
        ]);
    }

    /**
     * Error response (4xx/5xx)
     * @param string $message
     * @param int $status
     * @param mixed $data
     * @return JsonResponse
     */
    public static function error(string $message, int $status = 404, $data = null): JsonResponse
    {
        return response()->json([
            'code' => $status,
            'message' => __($message),
            'data' => $data
        ], $status);
    }

    /**
     * Success message
     * @param string $message
     * @param           $data
     * @return  JsonResponse
     */
    public static function success($message = null, $data = null)
    {
        return response()->json([
            'code' => 200,
            'message' => __($message),
            'data' => $data
        ],200);
    }
//    public static function paginatedSuccess($message = null, $data = null)
//    {
//        $data['code']=200;
//        $data['message'] = __($message);
//        return $data;
//    }

    /**
     * Authentication error
     * @param string $message
     * @param null $data
     * @param int $status
     * @return JsonResponse
     */
    public static function authenticationError($message = 'خطأ في تسجيل الدخول', $data = null, $status = 200)
    {
        return response()->json([
            'code' => 401,
            'message' => __($message),
            'data' => $data
        ], $status);
    }

    /**
     * Client error
     * @param string $message
     * @param         $data
     * @return JsonResponse
     */
    public static function clientError($message, $data = null)
    {
        return response()->json([
            'code' => 403,
            'message' => __($message),
        ],403);
    }

    /**
     * Client error
     * @param string $message
     * @param         $data
     * @return JsonResponse
     */
    public static function clientErrorAjax($message, $data = null, $inputs = [])
    {
        return response()->json([
            'code'    => 401,
            'message' => __($message),
            'inputs'  => $inputs
        ],401);
    }

    /**
     * Client not activated
     * @param string $message
     * @param         $data
     * @return JsonResponse
     */
    public static function clientNotActivated($message, $data = null)
    {
        return response()->json([
            'code' => 405,
            'message' => __($message),
        ],405);
    }

    /**
     * Server error
     * @param string $message
     * @param         $data
     * @return JsonResponse
     */
    public static function serverError($message, $data = null)
    {
        return response()->json([
            'code' => 500,
            'message' => __($message),
            'data' => $data
        ]);
    }

    /**
     * receives $validator->errors() and return it as string
     * @param Object $errors object of errors
     * @return string          errors string
     */
    public static function stringifyErrors($errors): string
    {
        $errorsString = '';
        foreach ($errors->toArray() as $key => $error) {
            $i=0;
            foreach ($error as $err) {
                if ($i > 0) $errorsString .= "\n";
                $errorsString .= $err;
                $i++;
            }
        }
        return $errorsString;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('api')->user();
            return $next($request);
        });
    }

    protected $enumStatuses = [
        'inactive', 'active'
    ];

    public function successResponse($result, $message, $responseCode)
    {
        //dd($result);
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $responseCode);
    }
    public function successResponse2($result, $roaster_hours, $working_hours, $net_pay, $message, $responseCode)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'roaster_hours' => $roaster_hours,
            'working_hours' => $working_hours,
            'net_pay' => $net_pay,
            'message' => $message,
        ];

        return response()->json($response, $responseCode);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function errorResponse($error, $errorMessages = [], $code = 404)
    {


        try {
            $response = [
                'success' => false,
                'message' => $error,
            ];


            if(!empty($errorMessages)){
                $response['data'] = $errorMessages;
            }


            return response()->json($response, $code);
        } catch (\Throwable $th) {
            return response()->json( $th->getMessage());
        }
    }
}

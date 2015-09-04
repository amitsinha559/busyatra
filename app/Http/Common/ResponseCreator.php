<?php namespace App\Http\Common;

use Illuminate\Support\Facades\Response;
use App\Http\Common\ValidateAndHandleError;

class ResponseCreator {
    
    public function successResponse($code, $message, $info=NULL){
        $response = Response::json(array(
            'error' => false,
            'code' => $code,
            'message' => $message,
            'info' => $info
        ), 200);

        return $response;
    }
    
    public function errorResponse($error, $message = null) {
        $validateAndHandleError = new ValidateAndHandleError();
        $response = Response::json(array(
            'error' => true,
            'info' => $validateAndHandleError->errorIdentifier($error)
        ));

        return $response;
    }
}
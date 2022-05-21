<?php

namespace App\Exceptions;

class CustomApiException {

    public static function error($code, $message = false, $description = false) {
        $body = [
            'status' => $code
        ];

        if(!$message) {
            if($code == 400)
                $body['message'] = __('error.badRequest');
            else if($code == 404)
                $body['message'] = __('error.notFound');
        } else {
            $body['message'] = $message;
        }

        if($description && $description != '')
            $body['description'] = $description;

        return response()->json($body, $code);
    }
}

<?php

function success ($message, $data, $code = 200) {
    return response()->json(
        [
            'result' => true,
            'message' => $message,
            'data' => $data
        ], 
        $code
    );
}

function fail ($message, $data, $code = 422) {
    return response()->json(
        [
            'result' => false,
            'message' => $message,
            'data' => $data
        ],
        $code
    );
}
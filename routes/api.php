<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::any('/pathao-web-hook', function (Request $request) {
    Log::info(
        'Pathao Web Hook Data:',
        [
            'data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString()
        ]
    );
    return response()->json(['status' => 'success']);
});

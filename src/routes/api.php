<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!'], 200);
});

Route::get('/', function () {
    return response()->json(['message' => 'Welcome, but try using an API client instead...'], 200);
});

Route::get('/doc', function () {
    return response()->json(['message' => 'this is the doc!'], 200);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

/**
 * @OA\Get(
 *     path="/user",
 *     operationId="getUser",
 *     tags={"User"},
 *     summary="Get Authenticated User",
 *     description="Returns the authenticated user.",
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             // ...define user schema properties here...
 *         )
 *     ),
 *     security={{ "sanctum": {} }}
 * )
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * @OA\Get(
 *     path="/test",
 *     operationId="getTestMessage",
 *     tags={"Test"},
 *     summary="Return test message",
 *     description="Returns a simple Hello World message.",
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Hello World!")
 *         )
 *     )
 * )
 */
Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!'], 200);
});

/**
 * @OA\Get(
 *     path="/",
 *     operationId="getWelcomeMessage",
 *     tags={"Welcome"},
 *     summary="Return welcome message",
 *     description="Returns a welcome message encouraging the use of an API client.",
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Welcome, but try using an API client instead...")
 *         )
 *     )
 * )
 */
Route::get('/', function () {
    return response()->json(['message' => 'Welcome, but try using an API client instead...'], 200);
});

/**
 * @OA\Get(
 *     path="/doc",
 *     operationId="getDocumentationMessage",
 *     tags={"Documentation"},
 *     summary="Return documentation message",
 *     description="Returns a simple documentation message.",
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="this is the doc!")
 *         )
 *     )
 * )
 */
Route::get('/doc', function () {
    return response()->json(['message' => 'this is the doc!'], 200);
});

Route::post('/test', [UserController::class, 'test']);

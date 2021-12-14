<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// AUTH
Route::post('/user/register', [UserController::class, 'register']);
Route::middleware('throttle:6,1')->post('/user/send-verify-email', [UserController::class, 'sendVerifyEmail']);
Route::post('/user/verify-email', [UserController::class, 'verifyEmail']);
Route::middleware('auth:api')->get('/user', [UserController::class, 'show']);
Route::middleware('auth:api')->post('/user/logout', [UserController::class, 'logout']);
// TODO
Route::middleware('auth:api')->apiResource('todo', TodoController::class);
Route::middleware('auth:api')->apiResource('blog', BlogController::class);

// PUBLIC
// Route::group(function () {
//     //
// });

// PRiVATE
// Route::middleware(['auth:api'])->group(function () {
//     //
// });


// BlogFile
Route::middleware('auth:api')->post('/blog/file', [BlogController::class, 'file']);

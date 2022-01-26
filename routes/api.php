<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SocialiteController;
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

// Auth
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::middleware('throttle:6,1')->post('/auth/send-verify-email', [AuthController::class, 'sendVerifyEmail']);
Route::post('/auth/verify-email', [AuthController::class, 'verifyEmail']);
Route::middleware('auth:api')->post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

// User
Route::middleware('auth:api')->get('/user', [UserController::class, 'show'])->name('user.show');
Route::middleware('auth:api')->put('/user', [UserController::class, 'update'])->name('user.update');
Route::middleware('auth:api')->put('/user/change-password', [UserController::class, 'changePassword'])->name('user.changePassword');

// Todo:CRUD
Route::middleware('auth:api')->apiResource('todo', TodoController::class);

// Blog:CRUD
Route::middleware('auth:api')->apiResource('blog', BlogController::class);

// Blog:/id/file
Route::middleware('auth:api')->post('/blog/{blog}/file', [BlogController::class, 'fileAdd'])->name('blog.file.add');
Route::middleware('auth:api')->delete('/blog/{blog}/file', [BlogController::class, 'fileDel'])->name('blog.file.del');

// Blog-Comment:list,create
Route::middleware('auth:api')->get('/blog/{blog}/comment', [BlogController::class, 'comment'])->name('blog.comment.list');
Route::middleware('auth:api')->post('/blog/{blog}/comment', [BlogController::class, 'commentCreate'])->name('blog.comment.create');

// Blog:/id/like
Route::middleware('auth:api')->put('/blog/{blog}/like', [BlogController::class, 'like'])->name('blog.like');

// Comment(Reply):CRUD
Route::middleware('auth:api')->get('/comment/{comment}', [CommentController::class, 'index'])->name('comment.list');
Route::middleware('auth:api')->post('/comment/{comment}', [CommentController::class, 'store'])->name('comment.store');
Route::middleware('auth:api')->put('/comment/{comment}', [CommentController::class, 'update'])->name('comment.update');
Route::middleware('auth:api')->delete('/comment/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');

// Comment(Reply):/id/like
Route::middleware('auth:api')->put('/comment/{comment}/like', [CommentController::class, 'like'])->name('comment.like');

// * example
// Route::group(function () {
// ...
// });

// * example:access token
// Route::middleware(['auth:api'])->group(function () {
// ...
// });

// Temporary File
Route::middleware('auth:api')->post('/file', [FileController::class, 'file'])->name('temporary.file.upload');

// Socialite singin
Route::post('/socialite/singin', [SocialiteController::class, 'singin']);

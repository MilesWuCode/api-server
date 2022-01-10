<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FileController;
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
Route::post('/user/register', [UserController::class, 'register'])->name('user.register');
Route::middleware('throttle:6,1')->post('/user/send-verify-email', [UserController::class, 'sendVerifyEmail']);
Route::post('/user/verify-email', [UserController::class, 'verifyEmail']);
Route::middleware('auth:api')->get('/user', [UserController::class, 'show'])->name('user.show');
Route::middleware('auth:api')->post('/user/logout', [UserController::class, 'logout'])->name('user.logout');

// TODO
Route::middleware('auth:api')->apiResource('todo', TodoController::class);

// Blog
Route::middleware('auth:api')->apiResource('blog', BlogController::class);

// Blog/id/file:add,del
Route::middleware('auth:api')->post('blog/{blog}/file', [BlogController::class, 'fileAdd'])->name('blog.file.add');
Route::middleware('auth:api')->delete('blog/{blog}/file', [BlogController::class, 'fileDel'])->name('blog.file.del');

// Blog/id/comment:list,create
Route::middleware('auth:api')->get('blog/{blog}/comment', [BlogController::class, 'comment'])->name('blog.comment.list');
Route::middleware('auth:api')->post('blog/{blog}/comment', [BlogController::class, 'commentCreate'])->name('blog.comment.create');

// Comment or Reply
Route::middleware('auth:api')->get('comment/{comment}', [CommentController::class, 'index'])->name('comment.list');
Route::middleware('auth:api')->post('comment/{comment}', [CommentController::class, 'store'])->name('comment.store');
Route::middleware('auth:api')->put('comment/{comment}', [CommentController::class, 'update'])->name('comment.update');
Route::middleware('auth:api')->delete('comment/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');

// PUBLIC
// Route::group(function () {
//     //
// });

// PRiVATE
// Route::middleware(['auth:api'])->group(function () {
//     //
// });

// Temporary File
Route::middleware('auth:api')->post('/file', [FileController::class, 'file'])->name('temporary.file.upload');

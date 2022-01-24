<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use Laravel\Socialite\Facades\Socialite;

// * example
Route::get('/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});

// * example
Route::get('/auth/callback', function () {
    $user = Socialite::driver('google')->user();

    dump($user);
});

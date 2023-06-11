<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Services\UserService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $reirev = new User(['name' => 'Rei Rev']);
    $reirev->save();

    $foo = new User(['name' => 'foo']);

    $userService = new UserService();
    dd(
        $userService->exists($reirev),
        $userService->exists($foo)
    );
    return view('welcome');
});

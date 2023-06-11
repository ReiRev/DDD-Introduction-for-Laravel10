<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

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
    $user1 = User::create(['name' => 'Rei Rev']);
    $user2 = User::create(['name' => 'Rei Rev']);
    dd($user1 == $user2);
    return view('welcome');
});

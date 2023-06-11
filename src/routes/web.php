<?php

use Illuminate\Support\Facades\Route;
use App\ValueObjects\FullName;

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
    $name1 = new FullName('Rei', 'Rev');
    $name2 = new FullName('Rei', 'Rev');
    $name3 = new FullName('Foo', 'Bar');
    dd(
        $name1,
        $name1->equals($name2),
        $name1->equals($name3),
        $name1 == 'Rei Rev',
        $name1 == $name2,
        $name1 == $name3,
        $name1 === $name2,
        $name1 === $name3
    );
    return view('welcome');
});

<?php

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

//use Illuminate\Routing\Route;

use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route; //pengganti yang diatas karena error

Route::get('/', function () {
    return view('welcome');
});

Route::group(
    ['namespace'=> 'Admin', 'prefix'=>'admin'],
    function(){
        Route::get('dashboard','DashboardController@index');
        Route::resource('categories','CategoryController');
    }
);

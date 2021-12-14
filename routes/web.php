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
    //untuk melindungi halaman di route ini dengan sistem login di auth
    ['namespace'=> 'Admin', 'prefix'=>'admin','middleware' => ['auth']],

    function(){
        Route::get('dashboard','DashboardController@index');
        Route::resource('categories','CategoryController');
        Route::resource('products','ProductController');
    }
);

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



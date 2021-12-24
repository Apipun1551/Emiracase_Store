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

Route::get('/', 'HomeController@index');
Route::get('/products','ProductController@index');

Route::group(
    //untuk melindungi halaman di route ini dengan sistem login di auth
    ['namespace'=> 'Admin', 'prefix'=>'admin','middleware' => ['auth']],

    function(){
        //dashboard
        Route::get('dashboard','DashboardController@index');
        //category
        Route::resource('categories','CategoryController');
        //products
        Route::resource('products','ProductController');
        //image_products
        Route::get('products/{productID}/images','ProductController@images')->name('products.images');
        Route::get('products/{productID}/add-image','ProductController@add_image')->name('products.add_image');
        Route::post('products/images/{productID}','ProductController@upload_image')->name('products.upload_image');
        Route::delete('products/images/{imageID}','ProductController@remove_image')->name('products.remove_image');
        //attribute product
        Route::resource('attributes','AttributeController');
        Route::get('attributes/{attributeID}/options', 'AttributeController@options')->name('attributes.options');//link option
        Route::get('attributes/{attributeID}/add-option', 'AttributeController@add_option')->name('attributes.add_option');//form menambahkan option
        Route::post('attributes/options/{attributeID}', 'AttributeController@store_option')->name('attributes.store_option');//menyimpan option
        Route::delete('attributes/options/{optionID}', 'AttributeController@remove_option')->name('attributes.remove_option');//mencghapus option
        Route::get('attributes/options/{optionID}/edit', 'AttributeController@edit_option')->name('attributes.edit_option');//mengubah option
        Route::put('attributes/options/{optionID}', 'AttributeController@update_option')->name('attributes.update_option');//untuk update option

        Route::resource('roles', 'RoleController');//Halaman roles
        Route::resource('users', 'UserController');//halaman user
    }
);

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



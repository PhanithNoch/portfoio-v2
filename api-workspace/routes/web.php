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
Route::get('/', function () {
    return view('welcome');
});


/** Guest Access */
Route::group(['namespace'=> 'API', 'middleware' => ['cors']], function(){ 

    Route::post('sendmail','MailController@index');
    Route::post('sendsms','SendsmsController@sendSms');

    Route::get('public-categories', 'CategoriesController@publicIndex');
    Route::get('public-products', 'ProductsController@publicProduct');
    Route::get('public-articles', 'PostsController@publicIndex'); 
    Route::get('public-search-articles', 'PostsController@publicSearch');
    Route::get('public-people', 'PersonAccountController@publicIndex');
    Route::get('public-image-gallery', 'PostsController@publicImageGallery');
    Route::get('public-company', 'CompanyController@publicCompany');
    Route::get('theme-page', 'SitePageController@publicIndex');
    Route::get('theme-section', 'SectionsController@index');
    Route::get('theme-menu', 'SiteNavController@publicIndex');
});

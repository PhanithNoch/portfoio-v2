<?php

use Illuminate\Http\Request;

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

/** Routes without auth go here */
Route::post('login', 'AuthController@logIn')->middleware(['cors','throttle_api:5,5']);//

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

//'throttle1' maximum number of requests that can be made in a given number of minutes.
//'throttle:60,1' 60 times per minute
Route::group(['middleware'=>['auth:api', 'throttle_api:200,1', 'cors']], function(){
    Route::post('logout', 'AuthController@logOut');

    Route::group(['namespace'=> 'API'], function(){

        /** user Route */
        //show: get a record, update: update a record, destroy: delete a record
        Route::apiResource('user', 'UsersController', ['only' => ['show', 'update', 'destroy']]);
        //index: get all records (filterable), store: mass create records
        Route::apiResource('users', 'UsersController', ['only' => ['index', 'store']]);
        Route::put('users', 'UsersController@updates');//mass update
        Route::get("user-profile", 'UsersController@userProfile'); 
        Route::post("change-password", "UsersController@changePassword");

        /** Company Route */
        Route::get("company-profile", "CompanyController@getInfo");
        Route::get("company-setting", "CompanyController@getAllConfig");

        Route::apiResource('company', 'CompanyController', ['only' => ['update', 'destroy']]); 
        Route::apiResource('companies', 'CompanyController', ['only' => ['index', 'store']]);
        Route::put('companies', 'CompanyController@updates');//mass update

        /** Company Contact Route */
        Route::apiResource('company-contact', 'ComapnyContactController', ['only' => ['update', 'destroy']]); 
        Route::apiResource('company-contacts', 'ComapnyContactController', ['only' => ['index', 'store']]);
        Route::put('company-contacts', 'ComapnyContactController@updates');//mass update
        Route::delete('company-contacts', 'ComapnyContactController@destroys');//mass delete

        /** Category route */
        Route::apiResource("category", "CategoriesController", ["only" => ['update', 'destroy']]);
        Route::apiResource("categories", "CategoriesController", ["only" => ['store', 'index']]);
        Route::put('categories', 'CategoriesController@updates');//mass update
        
        /** Tag route */
        Route::apiResource("tag", "TagsController", ["only" => ["update", "destroy"]]);
        Route::apiResource("tags", "TagsController", ["only" => ["store", "index"]]);
        Route::put('tags', 'TagsController@updates');//mass update

        /** Pricebook */
        // Route::apiResource("pricebook", "PricebookController", ["only" => ["update", "destroy"]]);
        // Route::apiResource("pricebooks", "PricebookController", ["only" => ["store", "index"]]);
        // Route::put('pricebooks', 'PricebookController@updates');//mass update

        /** Pricebook entry */
        // Route::apiResource("pricebook-entries", "PricebookEntryController", ["only" => ["store", "index"]]);
        // Route::put('pricebook-entries', 'PricebookEntryController@updates');//mass update
        // Route::delete('pricebook-entries', 'PricebookEntryController@destroys');//mass delete

        /** Person Accoun Route  */
        Route::apiResource("person", "PersonAccountController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("people", "PersonAccountController", ["only" => ['store', 'index']]);
        Route::put('people', 'PersonAccountController@updates');//mass update

        /** Site nav */
        Route::apiResource("site-nav", "SiteNavController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("site-navs", "SiteNavController", ["only" => ['store', "index"]]);
        Route::put('site-navs', 'SiteNavController@updates');//mass update

        /** Site Page */
        Route::apiResource("site-page", "SitePageController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("site-pages", "SitePageController", ["only" => ['store', "index"]]);
        Route::put('site-pages', 'SitePageController@updates');//mass update

        /** Section */
        Route::apiResource("section", "SectionsController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("sections", "SectionsController", ["only" => ['store', "index"]]);
        Route::put('sections', 'SectionsController@updates');//mass update

        /** asset */
        Route::apiResource("asset", "AssetsController", ["only" => ["show", 'update', 'destroy']]);
        Route::apiResource("assets", "AssetsController", ["only" => ['store', 'index']]);
        Route::put('assets', 'AssetsController@updates');//mass update

        /** Products Route  */
        Route::apiResource("product", "ProductsController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("products", "ProductsController", ["only" => ['store', 'index']]);
        Route::put('products', 'ProductsController@updates');//mass update

        /** Product category */
        Route::apiResource("product-category", "ProductCategoryController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("product-categories", "ProductCategoryController", ["only" => ['store']]);
        Route::put('product-categories', 'ProductCategoryController@updates');//mass update

        /** Product Photos */
        // Route::apiResource("product-photo", "ProductPhotoController", ["only" => ['show', 'update', 'destroy']]);
        // Route::apiResource("product-photos", "ProductPhotoController", ["only" => ['store']]);
        // Route::put('product-photos', 'ProductPhotoController@updates');//mass update
        // Route::delete('product-photos', 'ProductPhotoController@destroys');//mass delete

        /** Photos */
        Route::apiResource("photo", "PhotoController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("photos", "PhotoController", ["only" => ['store', 'index']]);
        Route::put('photos', 'PhotoController@updates');//mass update
        Route::delete('photos', 'PhotoController@destroys');//mass delete

        /** Documents */
        // Route::apiResource("document", "DocumentController", ["only" => ['show', 'update', 'destroy']]);
        // Route::apiResource("documents", "DocumentController", ["only" => ['store', 'index']]);
        // Route::put('documents', 'DocumentController@updates');//mass update
        // Route::delete('documents', 'DocumentController@destroys');//mass delete

        /** posts Route  */
        Route::apiResource("post", "PostsController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("posts", "PostsController", ["only" => ['index', 'store']]);
        Route::put('posts', 'PostsController@updates');//mass update
        Route::delete('posts', 'PostsController@destroys');//mass delete

        /** User Permission */
        Route::apiResource("app-permission", "AppPermissionController", ["only" => ['show', 'update']]);
        Route::apiResource("app-permissions", "AppPermissionController", ["only" => ['index']]);
        Route::put('app-permissions', 'AppPermissionController@updates');//mass update
        
        Route::apiResource("application", "ApplicationController", ["only" => ['show', 'update']]);
        Route::apiResource("applications", "ApplicationController", ["only" => ['index']]);
        Route::put('applications', 'ApplicationController@updates');//mass update

        Route::apiResource("object-permission", "ObjectPermissionController", ["only" => ['show', 'update']]);
        Route::apiResource("object-permissions", "ObjectPermissionController", ["only" => ['index']]);
        Route::put('object-permissions', 'ObjectPermissionController@updates');//mass update

        Route::apiResource("permission", "PermissionController", ["only" => ['show', 'update']]);
        Route::apiResource("permissions", "PermissionController", ["only" => ['index']]);
        // Route::put('permissions', 'PostsController@updates');//mass update

        Route::apiResource("login-histories", "LoginHistoryController", ["only" => ['index']]);

        // Route::get("fb-post", "FacebookController@postArticle");
    });
    
});



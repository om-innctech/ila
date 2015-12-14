<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

//For zipcode validation
Validator::extend('validate_zipcode', 'CustomValidator@validateZipcode');

//For age validation
Validator::extend('validate_age', 'CustomValidator@validateAge');


/*---------- User API---------*/
Route::get('api/v1/verifyEmail/{confirmationCode}', 'UserController@verifyEmail');

Route::post('api/v1/forgot', 'UserController@forgotPassword');

// For signing in
Route::post('api/v1/login', 'UserController@login');

// For signing out
Route::get('api/v1/logout','UserController@logout');

Route::post('api/v1/change-password', 'UserController@changePassword');

Route::get('api/v1/user/search/{term}', 'UserController@search');

Route::group(array('prefix'=>'api/v1/'),function(){
    Route::resource('user', 'UserController');
});

/*----------End of User API---------*/

Route::group(array('prefix'=>'api/v1/'),function(){
    
	Route::get('category/sub/{id}', 'CategoriesController@show_subcategories');
    Route::resource('category', 'CategoriesController');
    Route::get('products/search/{term}', 'ProductsController@searching');
    Route::resource('products', 'ProductsController');
    Route::resource('brands', 'BrandsController');
    Route::post('updatebrands/{id}', 'BrandsController@updatebrands');
    Route::resource('tags', 'TagsController');
    Route::resource('conditions', 'ConditionsController');
    Route::get('productsoptions/values/{id}', 'ProductsOptionsController@show_values');
    Route::post('productsoptions/values', 'ProductsOptionsController@store_values');
    Route::resource('productsoptions', 'ProductsOptionsController');
    Route::resource('optionsvalues', 'ProductsOptionsValuesController');
    Route::resource('collections', 'CollectionsController');
    
});


Route::filter('auth.token', function($route, $request)
{
    $api_token = $request->header('X-Auth-Token');
    
    $user =  User::where('api_token', '=', $api_token)->first();	
    
    if(!$user) {

        $response = Response::json(array(
            'statusCode' => 403,
            'statusDescription' => "Bad Request", 
			"errors" => "Not authenticated. Please login.")
        );

        $response->header('Content-Type', 'application/json');
        return $response;
    }
    elseif( time() > strtotime($user->expires_at) ){
        
        $response = Response::json(array(
            'statusCode' => 403,
            'statusDescription' => "Bad Request", 
			"errors" => "Token expired please re login." )
        );

        $response->header('Content-Type', 'application/json');
        return $response;
    }
    Auth::login($user);
});

<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'ProductsController@index');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/


Route::group(['middleware' => ['web']], function () {
    //
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::group(['middleware' => 'auth'], function () {
        Route::resource('lists', 'ShoppingListsController');
    });

    Route::resource('products', 'ProductsController');
    Route::resource('categories', 'CategoriesController');

    Route::get('price_changes', function() {
	    $changes = DB::select("select *, (prices[2] - prices[1]) as difference,
		    round((prices[2] - prices[1]) / prices[1], 2) as change
		    from price_changes
		    order by change
		    limit 50;");
	    return view('table', ['items' => $changes]);
    });
});

Route::group(['middleware' => 'api', 'prefix' => 'api', 'namespace' => 'Api'], function() {
    Route::get('/products', 'ProductsController@index');
    Route::get('/products/{id}', 'ProductsController@show');

    Route::post('/products', 'ProductsController@add');
    Route::put('/products/{barcode}', 'ProductsController@update');

    Route::post('/scans', 'ProductsController@add');

});

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
            $numberFormatter = new NumberFormatter('nl_NL', NumberFormatter::DECIMAL);
        $changes = DB::select("
		    select id, title, prices,
			    last_updated,
			    (prices[1] - prices[2]) as difference,
			    round((prices[1] - prices[2]) / prices[2], 2) as change
			    from price_changes
			    where last_updated >= now() - interval '1 week'
			    order by change asc
			    limit 250;
	    ");
        $changes = new Illuminate\Support\Collection($changes);
        $changes->map(function($item) {
            $item->prices = priceChanges($item->prices);
            return $item;
        });
        
        return view('price_changes', [
            'changes' => $changes,
            'currencyFormatter' => $numberFormatter,
        ]);
    });
});

Route::group(['middleware' => 'api', 'prefix' => 'api', 'namespace' => 'Api'], function() {
    Route::get('/products', 'ProductsController@index');
    Route::get('/products/{id}', 'ProductsController@show');

    Route::post('/products', 'ProductsController@add');
    Route::put('/products/{barcode}', 'ProductsController@update');

    Route::post('/scans', 'ProductsController@add');

});

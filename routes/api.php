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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix'=>'v1', 'middleware'=>'cors'],function(){


    Route::post('/scrap',           'Scraps\BookingScrapperController@scrapSearchByCityAndDate');
     Route::get('/scraphotel',           'Scraps\BookingScrapperController@scrapSearchByhotel');

   // Route::get('/getbookingcities', 'Scraps\BookingScrapperController@getCityDestinationsInfo');
    //Route::get('/getbookingcitiesdestinies', 'DataSources\BookingDataSourcesController@getAllBookingCitiesDestinies');

    //Route::post('/createuser', 'Auth\RegisterController@create');
    //Route::get('/getallusers', 'Users\UsersController@getAllUsers');

    /* Route::get('/scrapbyhotel', 'Scraps\BookingScrapperController@scrapByHotel');
    Route::get('/scrapphantom', 'Scraps\PhantomjsController@scrapPhantom');
    Route::get('/searchscrapphantom', 'Scraps\PhantomjsController@scrapPhantomBookingSearch');
    Route::get('/searchscrapcasper', 'Scraps\CasperjsController@scrapCasper'); */
});

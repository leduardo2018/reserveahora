<?php

use App\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
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



     Route::post('/register', 'Auth\RegisterController@register');
     Route::post('/login', 'Auth\LoginController@login');   
     Route::post('/logout','Auth\LoginController@logout');





Route::group(['prefix'=>'v1', 'middleware'=>'cors'],function(){

    Route::post('/scrap',           'Scraps\BookingScrapperController@scrapSearchByCityAndDate');
    Route::post('/scraphotel',           'Scraps\BookingScrapperController@scrapSearchByhotel');
     

     Route::get('/cities',           'Scraps\BookingScrapperController@autocomplete');
     Route::get('/allcities',    'Scraps\BookingScrapperController@allcity');
     
     // Route::get('/cities', function(){

     //        $querystring = Input::get('queryString');
     //        $cities = City::where('city', 'like', '%'.$querystring.'%')->get();
     //        return response()->json($cities);

     // });
   // Route::get('/getbookingcities', 'Scraps\BookingScrapperController@getCityDestinationsInfo');
    //Route::get('/getbookingcitiesdestinies', 'DataSources\BookingDataSourcesController@getAllBookingCitiesDestinies');

    //Route::post('/createuser', 'Auth\RegisterController@create');
    //Route::get('/getallusers', 'Users\UsersController@getAllUsers');

     Route::get('/scrapbyhotel', 'Scraps\BookingScrapperController@scrapByHotel');
    // Route::get('/scrapphantom', 'Scraps\PhantomjsController@scrapPhantom');
    // Route::get('/searchscrapphantom', 'Scraps\PhantomjsController@scrapPhantomBookingSearch');
    // Route::get('/searchscrapcasper', 'Scraps\CasperjsController@scrapCasper'); 


});


   Route::group(['prefix'=>'v1'
    // , 'middleware'=>['cors','auth:api']
],function(){


           Route::post('/book', 'Scraps\BookingScrapperController@book');
           Route::post('/booking', 'Scraps\BookingScrapperController@booking');


   });
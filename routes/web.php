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
    return redirect('/login');
});



Route::get('/scraping', 'ScrapingController@example');
Route::get('/scrapinghotel', 'Scraps\BookingScrapperController@scrapSearchByhotel');
Route::get('/autocomplete', 'ScrapingController@vista');
Route::get('/cities',       'ScrapingController@autocomplete');




 Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/infopayu', 'ScrapingController@infopayu');

Route::post('/booking', 'ScrapingController@booking');
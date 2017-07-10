<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['middleware' => []], function () {

    // File
    Route::group(['prefix' => 'files', 'namespace' => 'Api'], function () {
        Route::get('/download/{id}', 'FileController@getDownloadOne');
    });

    // Artisan
    Route::group(['prefix' => 'artisan'], function () {
        Route::get('reset', 'ArtisanController@getCommandReset');
    });

    // Test
    Route::group(['prefix' => 'test'], function () {
        Route::get('test', 'TestController@index');
    });

     Route::any('/{slug}', function () {
         return File::get(public_path() . '/home/index.html');
     })->where('slug', '([A-z\d-\/_.]+)?');

//    Route::get('/{any}', function ($any) {
//        return File::get(public_path() . '/home/index.html');
//    })->where('any', '.*');
});


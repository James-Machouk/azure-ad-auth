<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    if(config('azureAdAuth.override_default_login'))
    {
        Route::get('/login', 'JamesMachouk\azureAdAuth\AzureAdAuthController@signin')->name('login');
        Route::get('/register', 'JamesMachouk\azureAdAuth\AzureAdAuthController@signin');
        Route::get('/callback', 'JamesMachouk\azureAdAuth\AzureAdAuthController@azureCallback');
        Route::namespace('App\Http\Controllers')->group(function () {
            Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
        });
        Route::get('/adLogout','JamesMachouk\azureAdAuth\AzureAdAuthController@adLogout')->name('adLogout');
    }
});


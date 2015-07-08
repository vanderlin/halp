<?php 

// --------------------------------------------------------------------------
// Register | Login | Google+
// --------------------------------------------------------------------------
Route::get('register', ['uses'=>'UsersController@register']);
Route::get('login', ['uses'=>'UsersController@login']);
Route::get('logout', ['uses'=>'UsersController@logout']);
Route::get('oauth2callback', ['uses'=>'GoogleSessionController@oauth2callback']);
Route::post('link-google-account/{id}', ['uses'=>'GoogleSessionController@linkAccount', 'as'=>'google.link']);
Route::post('unlink-google-account/{id}', ['uses'=>'GoogleSessionController@unlinkAccount', 'as'=>'google.unlink']);

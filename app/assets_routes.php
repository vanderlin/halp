<?php 

// --------------------------------------------------------------------------
// Assets
// --------------------------------------------------------------------------	
Route::group(['prefix'=>'images'], function() {
	Route::get('/', ['uses'=>'AssetsController@index']);
	Route::get('{id}/{size?}', ['uses'=>'AssetsController@resizeImage']);
});

Route::group(['prefix'=>'audio'], function() {
	Route::get('{id}', ['uses'=>'AssetsController@audio']);
});

// ------------------------------------------------------------------------
Route::group(['prefix'=>'assets'], function() {
	Route::get('clear-cache', ['uses'=>'AssetsController@clearAllCache', 'as'=>'assets.clear-all-cache']);
	Route::get('{id}', ['uses'=>'AssetsController@show', 'as'=>'assets.show']);
	Route::get('{id}/clear-cache', ['uses'=>'AssetsController@clearCache', 'as'=>'assets.clear-cache']);
	Route::get('{id}/meta', ['uses'=>'AssetsController@meta', 'as'=>'assets.meta']);
	
	Route::post('/create', ['uses'=>'AssetsController@store', 'as'=>'assets.store']);
	Route::put('{id}', ['uses'=>'AssetsController@update', 'as'=>'assets.update']);
	
	Route::put('{id}/versions', ['uses'=>'AssetsController@storeVersion']);
	Route::delete('{asset_id}/versions/{version_id}', ['uses'=>'AssetsController@deleteVersion']);
	Route::delete('{id}', ['uses'=>'AssetsController@delete']);
	Route::post('upload', ['uses'=>'AssetsController@upload']);
});


<?php 

// ------------------------------------------------------------------------
Route::group(['prefix'=>'developer', 'before'=>['auth']], function() {
	Route::get('/', ['uses'=>'APIController@developer_page']);
	Route::post('create', ['uses'=>'APIController@create_client', 'as'=>'api.create.client']);
});

// ------------------------------------------------------------------------
Route::group(['prefix'=>'api', 'before'=>'auth.basic'], function() {
	
	Route::get('/', ['uses'=>'APIController@root_api']);
	Route::get('users', ['uses'=>'APIController@users']);
	Route::get('users/{id}', ['uses'=>'APIController@user']);
	Route::get('users/{id}/created_tasks', ['uses'=>'APIController@users_created_tasks']);
	Route::get('users/{id}/claimed_tasks', ['uses'=>'APIController@users_claimed_tasks']);
	Route::get('users/{id}/un_claimed_tasks', ['uses'=>'APIController@users_un_claimed_tasks']);
});

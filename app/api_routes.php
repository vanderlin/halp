<?php 
Route::group(['prefix'=>'api', 'before'=>'auth.basic'], function() {
	Route::get('users', ['uses'=>'APIController@users']);
	Route::get('users/{id}', ['uses'=>'APIController@user']);
	Route::get('users/{id}/created_tasks', ['uses'=>'APIController@users_created_tasks']);
	Route::get('users/{id}/claimed_tasks', ['uses'=>'APIController@users_claimed_tasks']);
	Route::get('users/{id}/un_claimed_tasks', ['uses'=>'APIController@users_un_claimed_tasks']);
});
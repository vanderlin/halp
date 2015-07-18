<?php 

Route::group(['prefix'=>'api', 'before'=>'auth.basic'], function() {
	Route::get('users', ['uses'=>'APIController@users']);
	Route::get('users/{id}', ['uses'=>'APIController@user']);
	Route::get('users/{id}/created_tasks', ['uses'=>'APIController@users_created_tasks']);
	Route::get('users/{id}/claimed_tasks', ['uses'=>'APIController@users_claimed_tasks']);
	Route::get('users/{id}/un_claimed_tasks', ['uses'=>'APIController@users_un_claimed_tasks']);

	// Route::get('users/{id}', function($id) {
	// 	return User::find($id);    
	// });
	// Route::get('users/{id}/created_tasks', function($id) {
	// 	return Task\Task::where('creator_id', '=', $id)->with('Project')->with('Claimer')->get();    
	// });
	// Route::get('users/{id}/claimed_tasks', function($id) {
	// 	return Task\Task::where('claimed_id', '=', $id)->with('Project')->with('Claimer')->get();    
	// });
	// Route::get('users/{id}/un_claimed_tasks', function($id) {
	// 	return Task\Task::unClaimed()->where('creator_id', '=', $id)->with('Project')->with('Claimer')->get();    
	// });
});

Route::get('s', function() {
    return Auth::user()->createdTasks;
});

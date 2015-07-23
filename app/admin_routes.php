<?php

// --------------------------------------------------------------------------
// Admin / Roles
// --------------------------------------------------------------------------
 Route::group(['prefix'=>'admin', 'before'=>'auth'], function() {
	
	
	Route::get('/', function() {
		return View::make('admin.index', ['users'=>User::all(), 'active_link'=>'index']);
	});

	// ------------------------------------------------------------------------
	// Notifications
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'notifications'], function() {
		Route::get('/', function() {
			return View::make('admin.notifications.index', ['active_link'=>'notifications', 'notifications'=>Notification::orderBy('created_at', 'DESC')->get()]);
		});
		Route::post('/', ['uses'=>'NotificationsController@store', 'as'=>'admin.notice.store']);
	});

	// ------------------------------------------------------------------------
	// Projects
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'projects'], function() {
		Route::get('/', function() {
			return View::make('admin.projects.index', ['active_link'=>'projects', 'projects'=>Project\Project::all()]);
		});
	});

   
	// ------------------------------------------------------------------------
	// Tags CMS
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'tags'], function() {
		Route::get('/', function() {
			return View::make('admin.tags.index', ['active_link'=>'tags']);
		});
		Route::get('{id}', function($id) {
			return View::make('admin.tags.edit', ['active_link'=>'tags', 'tag'=>Tag::find($id)]);
		});
		Route::post('/', ['uses'=>'TagsController@store']);
		Route::put('{id}', ['uses'=>'TagsController@update']);
		Route::delete('{id}', ['uses'=>'TagsController@destroy']);
	});

	// ------------------------------------------------------------------------
	// Users Roles & Permissions CMS
	// ------------------------------------------------------------------------
	Route::get('users', function() {
		return View::make('admin.index', ['users'=>User::all(), 'active_link'=>'index']);
	});

	Route::get('users/{id}', function($id) {
		return View::make('admin.edituser', ['user'=>User::find($id), 'active_link'=>'users']);
	});

	// edit a profile (*** ADMIN ONLY ***)
	Route::get('users/{id}/roles/edit', function($id) {
		$user = User::find($id);
		if (Auth::user()->hasRole('Admin')) {
			return View::make('admin.roles.edit-user-roles', ['user'=>$user, 'active_link'=>'index']);
		}
		return 'You do not have permissions to do this...';
	});

	Route::resource('roles', 'RolesController');
	Route::resource('permissions', 'PermissionsController');
	Route::put('users/{id}', ['uses'=>'UsersController@editUserRoles']);
	
	// admin only (master)
	Route::get('assets', function() {
		return View::make('admin.assets.index');
	});
	Route::get('assets/upload', function() {
		return View::make('admin.assets.upload-popup');
	});
	
	Route::get('assets/{id}/edit', function($id) {
		return View::make('admin.assets.edit-popup', ['asset'=>Asset::find($id)]);
	});

});
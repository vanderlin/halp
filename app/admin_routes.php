<?php

// --------------------------------------------------------------------------
// Admin / Roles
// --------------------------------------------------------------------------
 Route::group(['prefix'=>'admin', 'before'=>'auth'], function() {
	
	
	Route::get('/', function() {
		
		return View::make('admin.index', ['users'=>User::orderBy('created_at', 'DESC')->paginate(12), 'active_link'=>'index']);
	});


	Route::group(['prefix'=>'tests'], function() {
		Route::get('/', function() {
			return View::make('admin.tests', ['users'=>User::all(), 'active_link'=>'tests']);
		});
		Route::post('send', function() {

			$notice = new Notification;
			$notice->event = Input::get('event');
			$notice->task_id = Input::get('task_id');
			$notice->load('Task');
			$notice->task->creator_id = Input::get('creator_id');
			$notice->task->claimed_id = Input::get('claimed_id');

			$notice->task->load('creator');
			$notice->task->load('claimer');
			$status = false;
			if($notice->event == Notification::NOTIFICATION_NEW_TASK)
			{
				$users = User::where('username', '=', 'tvanderlin')->get();
				$emails = [];
				foreach ($users as $user) {
					if(substr($user->email, 0, strlen('fake_')) !== 'fake_') {
						array_push($emails, $user->email);
					}
				}
				$status = $notice->sendEmailToGroup($emails);
			}
			// someone deleted a task - you need to check if
			// this task has been claimed
			else if($notice->event == Notification::NOTIFICATION_TASK_DELETED) {
				$status = $notice->sendEmailToUser($notice->task->claimer);
			}


			// someone claimed your task
			else if($notice->event == Notification::NOTIFICATION_TASK_CLAIMED) {
				$status = $notice->sendEmailToUser($notice->task->creator);
			}


			return [
				'status'=>$status,
				'notice'=>$notice,
				'input'=>Input::all()
			];

			dd($notice);
		});
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
		return View::make('admin.index', ['users'=>User::orderBy('created_at', 'DESC')->paginate(12), 'active_link'=>'users']);
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
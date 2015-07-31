<?php

// --------------------------------------------------------------------------
// Admin / Roles
// --------------------------------------------------------------------------
 Route::group(['prefix'=>'admin', 'before'=>'auth'], function() {
	
	
	Route::get('/', function() {
		
		return View::make('admin.index', ['users'=>User::orderBy('created_at', 'DESC')->paginate(12), 'active_link'=>'index']);
	});


	Route::group(['prefix'=>'emails'], function() {
		
		Route::get('/', function() {
			return View::make('admin.emails.index', ['users'=>User::all(), 'active_link'=>'emails']);
		});

		Route::get('view-email', function() {

			$pre_render = Input::get('pre_render', false) == "on" ? true : false;

			$notice = new Notification;
			$notice->event = Input::get('event');
			$notice->task_id = Input::get('task_id');
			$notice->load('Task');
			$notice->task->creator_id = Input::get('creator_id');
			$notice->task->claimed_id = Input::get('claimed_id');

			$notice->task->load('creator');
			$notice->task->load('claimer');
			$data = [
				'task'=>$notice->task,
				'extra'=>'<a style="position: fixed; bottom:0; text-align:center;padding:25px 0;background-color:#FF6666;width:100%;color:white;font-family: Montserrat, Arial, sans-serif;text-transform:uppercase;font-size:12px;letter-spacing:1px;" href="/admin/emails">Back to Admin</a>'
			];

			$view_name = Notification::getViewEvent(Input::get('event'));
			$view = View::make($view_name, $data)->render();
			if(!$pre_render) {
				return $view;
			}
			$premailer = new ScottRobertson\Premailer\Request();
			$response = $premailer->convert($view);
			// $email = Input::get('email', 'vanderlin@gmail.com');
			$emails = ['vanderlin@gmail.com', 'tvanderlin@ideo.com'];

			if (Input::get('view', true)==true) {
				return $response->downloadHtml();
			}

			Mail::send('emails.render', ['html'=>$response->downloadHtml()], function($message) use($emails) {
				$message->bcc($emails, 'Halp')->subject('From '.Auth::user()->getName()." Halp Email Test ".uniqid());
			});
			return $emails;
			if(Input::has('send')) {
				Mail::send('emails.render', ['html'=>$response->downloadHtml()], function($message) use($email) {
					$message->to($email, 'Halp')->subject(Auth::user()->getName()."Halp Email Test ".uniqid());
				});
				return 'sent';
			}
			return $view;
		});


		Route::post('send', function() {

			
			$notice = new Notification;
			$notice->event = Input::get('event');
			$notice->task_id = Input::get('task_id');
			$notice->load('Task');
			$notice->task->creator_id = Input::get('creator_id')==""?NULL:Input::get('creator_id');
			$notice->task->claimed_id = Input::get('claimed_id')==""?NULL:Input::get('claimed_id');

			$notice->task->load('creator');
			$notice->task->load('claimer');
			$status = false;
			
			$emails = explode(',', Input::get('emails', 'tvanderlin@ideo.com'));

			$emails = array_filter($emails, function($a) {
				if(filter_var($a, FILTER_VALIDATE_EMAIL)) {
					return $a;
				}
			});

			
			$subject = Input::has('subject') ? Input::get('subject') : Auth::user()->getName()." Halp Email Test:".$notice->event." ".uniqid();
			
			$status = $notice->sendEmailToGroup($emails, $subject);
			/*
			if($notice->event == Notification::NOTIFICATION_NEW_TASK)
			{
				
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
			*/
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
	// Tasks
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'tasks'], function() {
		Route::get('/', function() {
				/*return [
				'total'=>Task::all()->count(),
				'active_tasks'=>Task::active()->count(),
				'expired_tasks'=>Task::expired()->count(),
				'calc'=>(Task::active()->count()+Task::expired()->count())
				];*/
			return View::make('admin.tasks.index', ['active_link'=>'tasks', 'tasks'=>Task::all(), 'active_tasks'=>Task::active()->get(), 'expired_tasks'=>Task::expired()->get()]);
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
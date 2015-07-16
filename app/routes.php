<?php


use Carbon\Carbon;
use Task\Task;
use Notification\Notification;

// ------------------------------------------------------------------------
Route::get('php', function() {
	phpinfo();
});
Route::get('env', function() {
	return [$_SERVER, Config::getEnvironment()];
});
Route::get('test', function() {

	$email = "faka_adams.jeanne@dicki.org";
	return strbool(substr($email, 0, strlen('fake_')) === 'fake_');
	

});

Route::get('most-tasks', function() {
    // $tags = DB::table('taggables')
    // ->groupBy('tag_id')->orderBy('count', 'DESC')->get(array('tag_id', DB::raw('count(*) as count')));

	return Task::whereNotNull('claimed_id')
				
                ->groupBy('claimed_id')
                ->select(array('*', DB::raw('count(*) as claimed_count')))
                ->orderBy('claimed_count', 'DESC')
                ->get();
});

Route::get('testemail', function() {

	
	$data = [
		'task'=>Task::first(),
	];


	$view = View::make('emails.new-task', $data)->render();

	$premailer = new ScottRobertson\Premailer\Request();
	$response = $premailer->convert($view);
	$email = Input::get('email', 'vanderlin@gmail.com');

	if(Input::has('send')) {
		Mail::send('emails.render', ['html'=>$response->downloadHtml()], function($message) use($email) {
			$message->to($email, 'Halp')->subject(Auth::user()->getName()."Halp Email Test ".uniqid());
		});
		return 'sent';
	}
	return $view;
});

// ------------------------------------------------------------------------
Route::group(array('prefix'=>'notifications'), function() {
	
	Route::get('/', function() {
		return View::make('site.notifications.index', ['notifications'=>Notification::all()]);
	});
	
	Route::any('send/{id}', ['uses'=>'NotificationsController@send']);
	
	Route::any('send', function() {

		// first get all users that want to receive notifications
		$users = User::where('notifications', '=', 1)->get();

		// get all notifications that have not been sent out
		$notifications = Notification::whereNull('sent_at')->get();
		$results = [];
		
		$preview = Input::get('preview', false);
		if($preview)
		{
			return View::make('emails.new-task', ['task'=>Task::first()]);
		}


		foreach ($notifications as $notice) {
			
			// New Task - send to all users that want to be notified
			if($notice->event == Notification::NOTIFICATION_NEW_TASK)
			{
				foreach ($users as $user) {
					$notice->sendEmailToUser($user);
				}
			}

			// someone claimed your task
			else if($notice->event == Notification::NOTIFICATION_TASK_CLAIMED) {
				$notice->sendEmailToUser($notice->task->creator);
		}
		}
		return  $results;
		
		return Redirect::back()->with(['notice'=>'Notifications Sent']);
	});

	// $preview = Input::get('preview', false);

	// Route::get('newtask', function() use($preview) {

	// 	if($preview) {
	// 		$task = 
	// 		return View::make('emails.new-task');
	// 	}

	// 	Mail::send('emails.new-task', array('key' => 'value'), function($message) {
 //    		$message->to('vanderlin@gmail.com', 'Todd Vanderlin')->subject('Welcome!');
	// 	});
	// 	//return View::make('emails.new-task');	

	// });
});

// ------------------------------------------------------------------------
Route::post('site-login', ['uses'=>'PageController@ChecksiteLogin']);

// ------------------------------------------------------------------------
Route::group(array('before'=>['auth']), function() {


	Route::get('/', ['uses'=>'TasksController@index']);
		
	Route::get('leaderboard', ['uses'=>'UsersController@index']);

	// projects
	Route::group(array('prefix'=>'projects'), function() {
		Route::get('/', ['uses'=>'ProjectsController@index']);
		Route::get('{id}', ['uses'=>'ProjectsController@show']);
		Route::get('search', ['uses'=>'ProjectsController@search']);
	});

	// users
	Route::group(array('before'=>['auth', 'confirmed']), function() {
		include 'user_routes.php';
	});

	// tasks
	Route::group(array('prefix'=>'tasks', 'before'=>'auth'), function() {
		Route::post('/', ['uses'=>'TasksController@store', 'as'=>'tasks.store']);
		Route::get('{id}', ['uses'=>'TasksController@show', 'as'=>'tasks.show']);
		Route::get('{id}/claimed', ['uses'=>'TasksController@showClaimed', 'as'=>'tasks.show.claimed']);
		Route::post('{id}/claim', ['uses'=>'TasksController@claim', 'as'=>'tasks.claim']);
		Route::post('{id}/unclaim', ['uses'=>'TasksController@unclaim', 'as'=>'tasks.unclaim']);
		Route::delete('{id}', ['uses'=>'TasksController@delete', 'as'=>'tasks.delete']);
	});


});



// ------------------------------------------------------------------------
include 'seeder_routes.php';
include 'assets_routes.php';
include 'auth_routes.php';
include 'admin_routes.php';


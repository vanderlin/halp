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

// ------------------------------------------------------------------------
Route::get('testemail', function() {

	
	$data = [
		'task'=>Task::first(),
	];


	$view = View::make('emails.new-task', $data)->render();

	$premailer = new ScottRobertson\Premailer\Request();
	$response = $premailer->convert($view);
	// $email = Input::get('email', 'vanderlin@gmail.com');
	$emails = ['vanderlin@gmail.com', 'tvanderlin@ideo.com'];
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


// notifications
// ------------------------------------------------------------------------
Route::group(array('prefix'=>'notifications'), function() {
	
	// send a notification
	Route::any('send/{id}', ['uses'=>'NotificationsController@send']);

});

// site login
// ------------------------------------------------------------------------
Route::post('site-login', ['uses'=>'PageController@ChecksiteLogin']);

// ------------------------------------------------------------------------
Route::group(array('before'=>['auth']), function() {

	Route::get('unsubscribe', ['uses'=>'UsersController@unsubscribe']);
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
		Route::get('create', ['uses'=>'TasksController@create', 'as'=>'tasks.create']);
		Route::get('edit/{id}', ['uses'=>'TasksController@edit', 'as'=>'tasks.edit']);
		Route::get('{id}', ['uses'=>'TasksController@show', 'as'=>'tasks.show']);
		Route::put('{id}', ['uses'=>'TasksController@update', 'as'=>'tasks.update']);
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
include 'api_routes.php';

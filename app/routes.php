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

Route::get('most-tasks', function() {
    // $tags = DB::table('taggables')
    // ->groupBy('tag_id')->orderBy('count', 'DESC')->get(array('tag_id', DB::raw('count(*) as count')));

	return Task::whereNotNull('claimed_id')
				
                ->groupBy('claimed_id')
                ->select(array('*', DB::raw('count(*) as claimed_count')))
                ->orderBy('claimed_count', 'DESC')
                ->get();
});


// ------------------------------------------------------------------------
Route::group(array('prefix'=>'notifications'), function() {
	
	Route::get('/', function() {
		return View::make('site.notifications.index', ['notifications'=>Notification::all()]);
	});

	Route::any('send', function() {

		// first get all users that want to receive notifications
		$users = User::where('notifications', '=', 1)->get();

		// get all notifications that have not been sent out
		$notifications = Notification::whereNull('sent_at')->get();
		$results = [];
		
		$preview = Input::get('preview', false);
		if($preview)
		{
			return View::make('emails.new-task', ['user'=>Auth::user()]);
		}
		foreach ($notifications as $notice) {
			


			Mail::send('emails.new-task', array('key' => 'value'), function($message) use($users, $results, $notice) {
				foreach ($users as $user) {
					$subject = $notice->task->creator->getShortName().' Needs Help';
					$message->to($user->email, $user->getName())->subject($subject);
				}
			});

		}
		
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
Route::group(array('before'=>['siteprotection']), function() {


	Route::get('/', ['uses'=>'TasksController@index']);
		
	Route::get('leaderboard', ['uses'=>'UsersController@index']);

	// projects
	Route::group(array('prefix'=>'projects'), function() {
		Route::get('/', ['uses'=>'ProjectsController@index']);
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
	});


});



// ------------------------------------------------------------------------
include 'seeder_routes.php';
include 'assets_routes.php';
include 'auth_routes.php';
include 'admin_routes.php';


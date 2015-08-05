<?php


use Carbon\Carbon;
use Task\Task;
use Notification\Notification;

Route::any('test-query', function() {

	foreach (Award::getAwards() as $type) {
		preg_match('/\d+/', $type->name, $matches);
		if($matches) $id = $matches[0];
		echo "$id<br>";
	}
	dd('');

	$start = Task::orderBy('created_at')->first()->created_at;
	$end   = Carbon::now();
	$res = [];
	$k = 0;
	$date = clone $start;
	for ($i=$start->weekOfYear; $i<=$end->weekOfYear; $i++) {
		
		$quary_award = Award::awardsForWeek(Award::AWARD_MOST_TASK_CREATED_WEEK, $date)->first();	

		array_push($res, ['i'=>$i, 'inc'=>$date->weekOfYear, $quary_award]);
		$date->addWeek();
	}
		return [
			$start->weekOfYear,
			$end->weekOfYear,
			$res
		];

	return Project::orderByMostTasks()->with('user')->get();

	$last_week = last_week();
	$user = User::mostHelpfulForProject()->first();
	return [$user, Project::find($user->most_helped_project)];
		// with('claimedTasks')
		// ->join('tasks', 'users.id', '=', 'tasks.claimed_id')
		// select([
		// 	'users.*',
		// 	DB::raw($sql)
		// ]);

	// ->sortByDesc(function($item) {
 //        return $item->claimedTasks->count();
 //    })->each(function($item) {
 //        return $item->totalClaimedTasks = $item->claimedTasks->count();
 //    })->values()->first();

    // return $leader->toSql();
    return $leader->first();
	$q = Task::query();
	$q->notExpired()->withIsExpired();
	
	
	// $q->whereRaw("IFNULL(`task_date`, `created_at`) > '$today'");
	
	
	// $q->whereRaw(DB::raw("
	// 	IFNULL(`task_date`, `created_at`) >
	// 		(CASE WHEN `task_date` IS NULL THEN DATE_ADD(CURDATE(), INTERVAL $n_days DAY) ELSE CURRENT_DATE END)"));
	//$q->select('tasks.*', DB::raw("IFNULL(`task_date`, `created_at`) > (CASE WHEN `task_date` IS NULL THEN DATE_ADD(CURDATE(), INTERVAL $n_days DAY) ELSE CURRENT_DATE END) AS is_expired"));
	$q->withTrashed();
	// return $q->toSql();
	return $q->get();
	return $r;
});
// ------------------------------------------------------------------------
Route::get('php', function() {
	phpinfo();
});
Route::get('env', function() {
	return [$_SERVER, Config::getEnvironment()];
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
		
	// leaderboard
	Route::get('leaderboard', ['uses'=>'UsersController@index']);

	// feedback
	Route::group(array('prefix'=>'feedback'), function() {
		Route::get('/', ['uses'=>'PageController@feedback']);
		Route::post('/', ['uses'=>'PageController@store_feedback', 'as'=>'feedback.store']);
	});


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

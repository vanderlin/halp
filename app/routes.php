<?php


use Carbon\Carbon;
use Task\Task;

// ------------------------------------------------------------------------
Route::get('php', function() {
	phpinfo();
});
Route::get('env', function() {
	return [$_SERVER, Config::getEnvironment()];
});

// ------------------------------------------------------------------------
Route::post('site-login', ['uses'=>'PageController@ChecksiteLogin']);

// ------------------------------------------------------------------------
Route::group(array('before'=>['siteprotection']), function() {


	Route::get('/', ['uses'=>'TasksController@index']);
	Route::get('projects', ['uses'=>'ProjectsController@index']);
	Route::group(array('before'=>['auth', 'confirmed']), function() {
		include 'user_routes.php';
	});
});



// ------------------------------------------------------------------------
include 'seeder_routes.php';
include 'assets_routes.php';
include 'auth_routes.php';
include 'admin_routes.php';


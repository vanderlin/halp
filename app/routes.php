<?php


use Carbon\Carbon;

Route::get('php', function() {
	phpinfo();
});


Route::get('env', function() {
	return [$_SERVER, Config::getEnvironment()];
});

Route::post('site-login', ['uses'=>'PageController@ChecksiteLogin']);
Route::group(array('before'=>['siteprotection', 'auth', 'confirmed']), function() {
	Route::get('/', function() {
		return View::make('site.index');
	});
	include 'user_routes.php';
});

// ------------------------------------------------------------------------
Route::get('seeder/users', function() {
	$seeder = new LOFaker;
	return $seeder->createFakeUser();
});

// ------------------------------------------------------------------------
include 'assets_routes.php';
include 'auth_routes.php';
include 'admin_routes.php';

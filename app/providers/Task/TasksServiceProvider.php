<?php namespace Task;

use Illuminate\Support\ServiceProvider;
use Task\TasksRepository;

/**
* Tasks ServiceProvider
*/

class TasksServiceProvider extends ServiceProvider  {
	
	// ------------------------------------------------------------------------
	public function register() 
	{
		$this->app->singleton('TasksRepository', function() {
			return new TasksRepository;
		});
	}
}
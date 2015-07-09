<?php namespace Project;

use Illuminate\Support\ServiceProvider;
use Project\ProjectsRepository;

/**
* Projects ServiceProvider
*/

class ProjectsServiceProvider extends ServiceProvider  {
	
	// ------------------------------------------------------------------------
	public function register() 
	{
		$this->app->singleton('ProjectsRepository', function() {
			return new ProjectsRepository;
		});
	}
}	
<?php namespace Asset;

use Illuminate\Support\ServiceProvider;
use Asset\AssetsRepository;

/**
* Assets ServiceProvider
*/

class AssetsServiceProvider extends ServiceProvider  {
	
	// ------------------------------------------------------------------------
	public function register() 
	{
		$this->app->singleton('AssetsRepository', function() {
			return new AssetsRepository;
		});
	}
}
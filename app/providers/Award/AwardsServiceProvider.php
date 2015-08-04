<?php namespace Award;

use Illuminate\Support\ServiceProvider;
use Award\AwardsRepository;

/**
* Awards ServiceProvider
*/

class AwardsServiceProvider extends ServiceProvider  {
	
	// ------------------------------------------------------------------------
	public function register() 
	{
		$this->app->singleton('AwardsRepository', function() {
			return new AwardsRepository;
		});
	}
}	
<?php namespace Notification;

use Illuminate\Support\ServiceProvider;
use Notification\NotificationsRepository;
use Notification\NotificationObserver;

/**
* Notifications ServiceProvider
*/

class NotificationsServiceProvider extends ServiceProvider  {
	
	// ------------------------------------------------------------------------
	public function register() 
	{
		$this->app->singleton('NotificationObserver', function($app) {
			return new NotificationObserver;
		});

		$this->app->singleton('NotificationRepository', function($app) {
			return new NotificationRepository;
		});

		$observer = $this->app->make('NotificationObserver');
		$observer->registerListeners();
	}
}
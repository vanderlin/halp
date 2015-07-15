<?php namespace Notification;

use Carbon\Carbon;
use Event;
use App;
use Log;
use Auth;
use User;
use Notification\Notification;

/**
* Notification Observer
*/
class NotificationObserver {

	private $timeframe = 30; // mins

	// ------------------------------------------------------------------------
	public function createNotification($event)
	{

		$exist = Notification::where('task_id', '=', $event['task']->id)->where('event', '=', $event['name'])->first();
		if($exist == NULL)
		{
			$notification = new Notification(['task_id'=>$event['task']->id, 'event'=>$event['name']]);
			$notification->save();		
		}
	}

	// ------------------------------------------------------------------------
	public function registerListeners()
	{
		Event::listen(Notification::NOTIFICATION_NEW_TASK, function($e) {
			$this->createNotification($e);
		});
		Event::listen(Notification::NOTIFICATION_TASK_CLAIMED, function($e) {
			$this->createNotification($e);
		});
	}
}


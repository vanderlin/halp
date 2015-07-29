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

	// ------------------------------------------------------------------------
	public function createNotification($event)
	{

		$type = get_class($event['object']);
		$id = $event['object']->id;

		$exist = Notification::where('object_id', '=', $id)
							 ->where('object_type', '=', $type)
							 ->where('event', '=', $event['name'])->first();
		if($exist == NULL)
		{
			$notification = new Notification(['object_type'=>$type, 'object_id'=>$id, 'event'=>$event['name']]);
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
		Event::listen(Notification::NOTIFICATION_HALP_WELCOME, function($e) {
			$this->createNotification($e);
		});
	}
}


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
		$name = $event['name'];

		$exist = Notification::where('object_id', '=', $id)
							 ->where('object_type', '=', $type)
							 ->where('event', '=', $name)->first();
		
		$notification = Notification::create(['object_type'=>$type, 'object_id'=>$id, 'event'=>$name]);



		$notification->save();


		if($exist == NULL)
		{
			if(!$notification->id) {
				dd("Error Saving Notification", $notification);
			}		
		}
		
		return $notification;
	}

	// ------------------------------------------------------------------------
	public function registerListeners()
	{
		Event::listen(Notification::NOTIFICATION_NEW_TASK, function($e) {
			$notice = $this->createNotification($e);
			$notice->task_id  = $notice->object_id;
			$notice->save();
		});
		Event::listen(Notification::NOTIFICATION_TASK_EXPIRED, function($e) {
			$notice = $this->createNotification($e);
			$notice->task_id  = $notice->object_id;
			$notice->save();
		});
		Event::listen(Notification::NOTIFICATION_TASK_CLAIMED, function($e) {
			$notice = $this->createNotification($e);
			$notice->task_id = $notice->object_id;
			$notice->save();
		});
		Event::listen(Notification::NOTIFICATION_HALP_WELCOME, function($e) {
			$notice = $this->createNotification($e);
			$notice->user_id = $notice->object_id;
			$notice->save();
		});
	}
}


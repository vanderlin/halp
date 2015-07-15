<?php namespace Notification;

use BaseModel;
use User;
use Validator;
use Carbon;
use Mail;
use View;
use ScottRobertson\Premailer\Request as Premailer;

class Notification extends BaseModel {
	
	protected $fillable  = ['task_id', 'event'];
	public static $rules = [];

	const NOTIFICATION_NEW_TASK = "notification.new.task";
	const NOTIFICATION_TASK_CLAIMED = "notification.task.claimed";
	
	// ------------------------------------------------------------------------
	public static function checkForNotifications()
	{
		// first get all users that want to receive notifications
		$users = User::where('notifications', '=', 1)->get();

		// get all notifications that have not been sent out
		$notifications = Notification::whereNull('sent_at')->get();
		$results = [];
		foreach ($notifications as $notice) {
			
			// New Task - send to all users that want to be notified
			if($notice->event == Notification::NOTIFICATION_NEW_TASK)
			{
				foreach ($users as $user) {
					$notice->sendEmailToUser($user);
				}
			}

			// someone claimed your task
			else if($notice->event == Notification::NOTIFICATION_TASK_CLAIMED) {
				$notice->sendEmailToUser($notice->task->creator);
			}
		}
		return  $results;
	}

    // ------------------------------------------------------------------------
    public function toArray() 
    {
    	$array = parent::toArray();
     	return $array;
    }

	// ------------------------------------------------------------------------
	public function save(array $options = array()) 
	{
  		parent::save();
	}

	// ------------------------------------------------------------------------
	public function task()
	{
		return $this->belongsTo('Task\Task');
	}

	// ------------------------------------------------------------------------
	public function getViewPath()
	{
		switch ($this->event) {
			case Notification::NOTIFICATION_NEW_TASK:
				return 'emails.new-task';
				break;
			case Notification::NOTIFICATION_TASK_CLAIMED:
				return 'emails.task-claimed';
				break;
			default:
				return 'emails.new-task';
				break;
		}
	}

	// ------------------------------------------------------------------------
	public function getSubject()
	{
		switch ($this->event) {
			case Notification::NOTIFICATION_NEW_TASK:
				return $this->task->creator->getShortName().' Needs Help';
				break;
			case Notification::NOTIFICATION_TASK_CLAIMED:
				return $this->task->claimer->getShortName().' has claimed one of your tasks!';
				break;
			default:
				return 'Halp';
				break;
		}
	}

	// ------------------------------------------------------------------------
	public function sendEmailToUser($user)
	{	

		$view = View::make($this->getViewPath(), array('task'=>$this->task))->render();
		$premailer = new Premailer();
		$response = $premailer->convert($view);

		Mail::send('emails.render', ['html'=>$response->downloadHtml()], function($message) use($user) {
			$message->to($user->email, 'Halp')->subject($this->getSubject());
		});
		// Mail::send($this->getViewPath(), array('task'=>$this->task), function($message) use($user) {
		// 	$message->to($user->email, $user->getName())->subject($this->getSubject());
		// });

	}
}
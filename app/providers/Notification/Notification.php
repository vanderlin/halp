<?php namespace Notification;

use BaseModel;
use User;
use Validator;
use Carbon;
use Mail;
use View;
use Event;
use Auth;

class Notification extends BaseModel {
	
	protected $fillable  = ['object_type', 'object_id', 'event'];
	public static $rules = [];

	const NOTIFICATION_HALP_INVITE 	= "notification.halp.invite";
	const NOTIFICATION_HALP_WELCOME = "notification.halp.welcome";
	const NOTIFICATION_NEW_TASK 	= "notification.new.task";
	const NOTIFICATION_TASK_CLAIMED = "notification.task.claimed";
	const NOTIFICATION_TASK_DELETED = "notification.task.deleted";
	const NOTIFICATION_TASK_EXPIRED = "notification.task.expired";
	
	public static $eventTypes = [
		Notification::NOTIFICATION_NEW_TASK,
		Notification::NOTIFICATION_TASK_CLAIMED,
		Notification::NOTIFICATION_TASK_EXPIRED,
		Notification::NOTIFICATION_TASK_DELETED,
		Notification::NOTIFICATION_HALP_WELCOME,
		Notification::NOTIFICATION_HALP_INVITE,
	];
	
	// ------------------------------------------------------------------------
	public static function fire($object, $event)
	{
		Event::fire($event, array(['object'=>$object, 'name'=>$event])); 
	}

    // ------------------------------------------------------------------------
    public function toArray() 
    {
    	$array = parent::toArray();
     	return $array;
    }

    // ------------------------------------------------------------------------
    public function scopeForEvent($query, $event)
    {
    	return $query->where('event', '=', $event);
    }
  	
    // ------------------------------------------------------------------------
    public function contextUser()
    {
    	switch ($this->event) {
			case Notification::NOTIFICATION_NEW_TASK:
				return $this->task->creator;
				break;
			case Notification::NOTIFICATION_TASK_CLAIMED:
				return $this->task->claimer;
				break;
			case Notification::NOTIFICATION_TASK_EXPIRED:
				return $this->task->creator;
				break;
			case Notification::NOTIFICATION_TASK_DELETED:
				return $this->task->creator;
				break;
			case Notification::NOTIFICATION_HALP_WELCOME:
				return $this->user;
				break;
			default:
				return $this->creator;
				break;
		}
    }
    // ------------------------------------------------------------------------
    public function getTitle()
    {
    	if($this->task != null) {
    		return $this->task->title;
    	}
    	else if($this->user != null) {
    		return $this->user->getName();
    	}
    	return "No Title :".$this->id.":".$this->event;
    }
    // ------------------------------------------------------------------------
    public function getIsSentAttribute($val)
    {
    	return $this->sent_at != NULL;
    }
    
    // ------------------------------------------------------------------------
	public function getSentAtAttribute($val)
	{
		return $val ? new Carbon\Carbon($val) : NULL;
	}

	// ------------------------------------------------------------------------
	public function save(array $options = array()) 
	{
  		parent::save();
	}

	

	// ------------------------------------------------------------------------
	// public function task()
	// {	
	// 	// return $this->belongsTo('Task\Task', 'object_id')->withTrashed();
	// 	// return $this->join('tasks', $this->object_id, '=', 'task.id');
	// 	// return $this->object_type == 'Task\Task' ? $this->belongsTo('Task\Task', 'object_id') : null;
	// 		// public function morphOne($related, $name, $type = null, $id = null, $localKey = null)

	// 	// return $this->morphedByMany('Task\Task','object');
	// 	// return $this->joing('Task\Task', 'object_id')->where('notification.object_type', '=', 'Task\Task')->withTrashed();
	// 	// return $this->hasOne('Task\Task')->join('tasks', 'notification.');//where('object_type', '=', 'Task\Task')->withTrashed();
	// }
	public function task()
	{
		return $this->belongsTo('Task\Task', 'task_id')->withTrashed();
	}

	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}

	// ------------------------------------------------------------------------
	public function getViewPath()
	{
		return Notification::getViewEvent($this->event);
	}
	public static function getViewEvent($event)
	{
		switch ($event) {
			case Notification::NOTIFICATION_NEW_TASK:
				return 'emails.new-task';
				break;
			case Notification::NOTIFICATION_TASK_CLAIMED:
				return 'emails.task-claimed';
				break;
			case Notification::NOTIFICATION_TASK_DELETED:
				return 'emails.task-deleted';
				break;
			case Notification::NOTIFICATION_TASK_EXPIRED:
				return 'emails.task-expired';
				break;
			case Notification::NOTIFICATION_HALP_INVITE:
				return 'emails.invite';
				break;
			case Notification::NOTIFICATION_HALP_WELCOME:
				return 'emails.welcome';
				break;
			default:
				return 'emails.new-task';
				break;
		}
	}

	// ------------------------------------------------------------------------
	public function getAction()
	{
		switch ($this->event) {
			case Notification::NOTIFICATION_NEW_TASK:
				return 'Created';
				break;
			case Notification::NOTIFICATION_TASK_CLAIMED:
				return 'Claimed';
			case Notification::NOTIFICATION_TASK_DELETED:
				return 'Deleted';
			default:
				return 'unkown';
				break;
		}
	}

	// ------------------------------------------------------------------------
	public function getSubject()
	{
		switch ($this->event) {
			case Notification::NOTIFICATION_NEW_TASK:
				return $this->task->creator->getShortName().' Needs Help with '.$this->task->title;
				break;
			case Notification::NOTIFICATION_TASK_CLAIMED:
				return $this->task->claimer->getShortName().' has claimed your task - '.$this->task->title;
				break;
			case Notification::NOTIFICATION_TASK_DELETED:
				return $this->task->creator->getShortName().' has removed a task you claimed!';
				break;
			case Notification::NOTIFICATION_HALP_WELCOME:
				return "Welcome to Halp";
				break;
			default:
				return 'Halp';
				break;
		}
	}

	// ------------------------------------------------------------------------
	public function send($debug=false)
	{	

		$this->sent_at = Carbon\Carbon::now();
		$this->save();
		$results = [];


		// this is a new task...
		if($this->event == Notification::NOTIFICATION_NEW_TASK)
		{
			$users = User::where('notifications', '=', 1)->where('id', '<>', $this->task->creator_id)->get();
			$emails = [];
			foreach ($users as $user) {
				if(substr($user->email, 0, strlen('fake_')) !== 'fake_') {
					array_push($emails, $user->email);
				}
			}
			if($debug) {
				$emails = ["tvanderlin@ideo.com"];
			}
			$results = $this->sendEmailToGroup($emails);
		}

		// Welcome to halp
		else if($this->event == Notification::NOTIFICATION_HALP_WELCOME) {
			$results = $this->sendEmailToUser($this->user);
		}


		// someone deleted a task - you need to check if
		// this task has been claimed
		else if($this->event == Notification::NOTIFICATION_TASK_DELETED) {
			$results = $this->sendEmailToUser($this->task->claimer);
		}


		// someone claimed your task
		else if($this->event == Notification::NOTIFICATION_TASK_CLAIMED) {
			$results = $this->sendEmailToUser($this->task->creator);
		}


		return $results;
	}

	// ------------------------------------------------------------------------
	public function sendEmailToGroup($group, $subject=null)
	{

		$view = View::make($this->getViewPath(), array('task'=>$this->task))->render();
		$premailer = new \ScottRobertson\Premailer\Request();
		$response = $premailer->convert($view);

		Mail::send('emails.render', ['html'=>$response->downloadHtml()], function($message) use($subject, $group) {			
			$message->bcc($group, 'Halp')->subject($subject?$subject:$this->getSubject());
		});
		return true;
	}
	// ------------------------------------------------------------------------
	public function sendEmailToUser($user, $subject=null)
	{	

		$view = View::make($this->getViewPath(), array('task'=>$this->task))->render();
		$premailer = new \ScottRobertson\Premailer\Request();
		$response = $premailer->convert($view);

		if(substr($user->email, 0, strlen('fake_')) !== 'fake_') {
			Mail::send('emails.render', ['html'=>$response->downloadHtml()], function($message) use($subject, $user) {
				$message->to($user->email, 'Halp')->subject($subject?$subject:$this->getSubject());
			});
		}
		return true;
	}
}
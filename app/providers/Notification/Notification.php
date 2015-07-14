<?php namespace Notification;

use BaseModel;
use User;
use Validator;
use Carbon;


class Notification extends BaseModel {
	
	protected $fillable  = ['task_id', 'event'];
	public static $rules = [];

	const NOTIFICATION_NEW_TASK = "notification.new.task";
	
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
}
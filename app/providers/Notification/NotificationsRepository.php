<?php namespace Notification;

use Carbon\Carbon;
use DB;
use \Illuminate\Support\Collection as Collection;
use Input;
use Paginator;
use User;
use Str;

/**
* Notifications Repository
*/

class NotificationsRepository  {
	
	private $listener;

	// ------------------------------------------------------------------------
	public function __construct() {
		
	}

	// ------------------------------------------------------------------------
	public function setListener($listener) {
		$this->listener = $listener;
	}

	// ------------------------------------------------------------------------
	public function find($id) {
		
		if(is_object($id)) {
			$id = $id->id;
		}
		$notification = Notification::withTrashed()->whereId($id)->first();
		return $this->listener->statusResponse(['notification'=>$notification]);		
	}

	// ------------------------------------------------------------------------
	public function store()
	{		
		return $this->listener->statusResponse(['notice'=>'Notification Created', 'notification'=>$notification]);		
	}

	// ------------------------------------------------------------------------
	public function delete($id) 
	{
		if(is_object($id)) {
			$id = $id->id;
		}
		$notification = Notification::withTrashed()->whereId($id)->first();
		if($notification) 
		{
			$notification->delete();
		}
		return $this->listener->statusResponse(['notification'=>$notification]);
	}
}
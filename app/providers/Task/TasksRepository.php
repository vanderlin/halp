<?php namespace Task;

use Carbon\Carbon;
use DB;
use \Illuminate\Support\Collection as Collection;
use Input;
use Paginator;
use User;
use Str;

/**
* Tasks Repository
*/

class TasksRepository  {
	
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
		$task = Task::withTrashed()->whereId($id)->first();
		return $this->listener->statusResponse(['task'=>$task]);		
	}

	// ------------------------------------------------------------------------
	public function store($input)
	{		
		$task = new Task;
		return $this->listener->statusResponse(['notice'=>'Task Created', 'task'=>$task]);		
	}

	// ------------------------------------------------------------------------
	public function delete($id) 
	{
		if(is_object($id)) {
			$id = $id->id;
		}
		$task = Task::withTrashed()->whereId($id)->first();
		if($task) 
		{
			$task->delete();
		}
		return $this->listener->statusResponse(['task'=>$task]);
	}
}	
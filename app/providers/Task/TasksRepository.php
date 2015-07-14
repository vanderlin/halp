<?php namespace Task;

use Carbon\Carbon;
use DB;
use \Illuminate\Support\Collection as Collection;
use Input;
use Paginator;
use User;
use Str;
use Validator;
use Project\Project;
use Auth;
use Notification\Notification;
use Event;

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
	public function get($id) {
		
		if(is_object($id)) {
			$id = $id->id;
		}
		return Task::withTrashed()->whereId($id)->first();
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
	public function claim($id) {
		
		if(is_object($id)) {
			$id = $id->id;
		}
		$task = Task::withTrashed()->whereId($id)->first();
		if($task) 
		{
			$task->claimed_id = Auth::id();
			$task->claimed_at = Carbon::now();
			$task->save();
		}
		return $this->listener->statusResponse(['task'=>$task]);		
	}

	// ------------------------------------------------------------------------
	public function unclaim($id) {
		
		if(is_object($id)) {
			$id = $id->id;
		}
		$task = Task::withTrashed()->whereId($id)->first();
		if($task) 
		{
			$task->claimed_id = NULL;
			$task->claimed_at = NULL;
			$task->save();
		}
		return $this->listener->statusResponse(['task'=>$task]);		
	}


	// ------------------------------------------------------------------------
	public function store($input)
	{		
		$validator = Validator::make($input, Task::$rules);
		if($validator->fails()) {
			return $this->listener->errorResponse($validator->errors()->all());
		}

		$project = Project::where('title', '=', $input['project'])->first();

		// if null we need to create the new project
		if($project == NULL)
		{
			$project = new Project(['title'=>$input['project'], 'user_id'=>Auth::id()]);
			$project->save();
		}

		$task = new Task(['title'=>$input['title'], 'duration'=>$input['duration'], 'project_id'=>$project->id, 'creator_id'=>Auth::id()]);
		$task->save();

		// fire a new notification to the system
		Event::fire(Notification::NOTIFICATION_NEW_TASK, array(['task'=>$task, 'name'=>Notification::NOTIFICATION_NEW_TASK])); 
   
		return $this->listener->statusResponse(['notice'=>'Task Created. Help is on the way!', 'task'=>$task]);		
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
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
		$validator = Validator::make($input, Task::$rules);
		if($validator->fails()) {
			return $this->listener->errorResponse($validator->errors()->all());
		}

		$task = new Task;
		$project = Project::where('title', '=', $input['project'])->first();

		// if null we need to create the new project
		if($project == NULL)
		{
			$project = new Project(['title'=>$input['project'], 'user_id'=>Auth::id()]);
			$project->save();
		}
		dd($task, $input, $project);
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
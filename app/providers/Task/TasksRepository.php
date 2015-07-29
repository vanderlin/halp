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
use View;

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
	public function update($id)
	{
		$input = Input::all();
		if(is_object($id)) {
			$id = $id->id;
		}
		$task = Task::withTrashed()->whereId($id)->first();

		
		$ntd = new Carbon($input['task_date']);
		if(Input::has('task_date') && $task->date->eq($ntd) == false) {
			$task->task_date = $ntd;
		}

		// title
		if(Input::has('title') && $task->title != $input['title']) {
			$task->title = $input['title'];	
		}

		// projects
		if(Input::has('project') && $task->project->title != $input['project']) {

			if($task->project->tasks->count() == 0)
			{
				$task->project->delete();
			}

			$project = Project::where('title', '=', $input['project'])->first();

			// if null we need to create the new project
			if($project == NULL)
			{
				$project = new Project(['title'=>$input['project'], 'creator_id'=>Auth::id()]);
				$project->save();
			}

			$task->project_id = $project->id;
		}

		if (Input::has('details') && $task->details != $input['details']) {
			$task->details = $input['details'];
		}
		
		// save and update timestamp
		$task->touch();
		$task->save();

		// reload the task
		$task = Task::withTrashed()->whereId($id)->first();

		$view = null;
		if(Input::has('view')&&Input::get('view'))
		{
			$view = View::make('site.tasks.card', array('task' => $task, 'claimed'=>$task->isClaimed))->render();
		}
		return $this->listener->statusResponse(['notice'=>'Your Task is updated', 'task'=>$task, 'view'=>$view]);		
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

			// fire a new notification to the system
			Event::fire(Notification::NOTIFICATION_TASK_CLAIMED, array(['object'=>$task, 'name'=>Notification::NOTIFICATION_TASK_CLAIMED])); 
   
		}
		
		$task = Task::withTrashed()->whereId($id)->with('Claimer')->with('Creator')->first();

		$view = null;
		if(Input::has('view')&&Input::get('view'))
		{
			$view = View::make('site.tasks.card', array('task' => $task, 'show_button'=>false))->render();
		}
		$notice = View::make('site.tasks.claim-task-response', array('task' => $task))->render();
		return $this->listener->statusResponse(['task'=>$task, 'notice'=>$notice, 'view'=>$view]);		
	}

	// ------------------------------------------------------------------------
	public function unclaim($id) {
		
		if(is_object($id)) {
			$id = $id->id;
		}
		$task = Task::withTrashed()->whereId($id)->first();
		if($task) 
		{	
			
			
			
			// we may need to send an email here...
			if($task->notification)
			{

				$task->notification->delete();	
			}

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
			return $this->listener ? $this->listener->errorResponse($validator->errors()->all()) : $validator->errors()->all();
		}

		$creator_id = isset($input['creator_id'])?$input['creator_id']:Auth::id();
		$claimed_id = isset($input['claimed_id'])?$input['claimed_id']:NULL;

		$project = Project::where('title', '=', $input['project'])->first();

		// if null we need to create the new project
		if($project == NULL)
		{
			$project = new Project(['title'=>$input['project'], 'creator_id'=>$creator_id]);
			$project->save();
		}
	

		

		$data = [
			'title'=>$input['title'], 
			'duration'=>$input['duration'], 
			'claimed_id'=>$claimed_id, 
			'project_id'=>$project->id, 
			'creator_id'=>$creator_id,
			'details'=>isset($input['details'])?$input['details']:NULL,
			'task_date'=>isset($input['task_date'])?$input['task_date']:NULL,
			];
		

		$task = new Task($data);
		$task->save();
		$view = NULL;

		// fire a new notification to the system
		Event::fire(Notification::NOTIFICATION_NEW_TASK, array(['object'=>$task, 'name'=>Notification::NOTIFICATION_NEW_TASK])); 
 
		if(Input::has('view')&&Input::get('view')==true)
		{
			$view = View::make('site.tasks.card', array('task' => $task, 'claimed'=>false))->render();

		}
		$id = $task->id;
		$task = Task::where('id', '=', $id)->with('Creator')->first();
		return $this->listener ? $this->listener->statusResponse(['notice'=>'Task Created. Help is on the way!', 'task'=>$task, 'view'=>$view]) : $task;		
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
			if($task->isClaimed)
			{
				// fire a new notification to the system
				Event::fire(Notification::NOTIFICATION_NEW_TASK, array(['object'=>$task, 'name'=>Notification::NOTIFICATION_TASK_DELETED])); 
				
			}
			$task->delete();
			
		}
		return $this->listener->statusResponse(['task'=>$task]);
	}
}	
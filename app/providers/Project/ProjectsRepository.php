<?php namespace Project;

use Carbon\Carbon;
use DB;
use \Illuminate\Support\Collection as Collection;
use Input;
use Paginator;
use User;
use Str;

/**
* Projects Repository
*/

class ProjectsRepository  {
	
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
		$project = Project::withTrashed()->whereId($id)->first();
		return $this->listener->statusResponse(['project'=>$project]);		
	}

	// ------------------------------------------------------------------------
	public function store()
	{		
		return $this->listener->statusResponse(['notice'=>'Project Created', 'project'=>$project]);		
	}

	// ------------------------------------------------------------------------
	public function delete($id) 
	{
		if(is_object($id)) {
			$id = $id->id;
		}
		$project = Project::withTrashed()->whereId($id)->first();
		if($project) 
		{
			$project->delete();
		}
		return $this->listener->statusResponse(['project'=>$project]);
	}
}
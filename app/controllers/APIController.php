<?php

class APIController extends \BaseController {

	public function response($data)
	{
		return Input::get('pretty', false) ? Response::json($data, 200, [], JSON_PRETTY_PRINT) : Response::json($data);    
	}

	// ------------------------------------------------------------------------
	public function users()
	{
		return $this->response(User::all());    
	}

	// ------------------------------------------------------------------------
	public function user($id)
	{
		return $this->response(User::find($id));
	}

	// ------------------------------------------------------------------------
	public function users_created_tasks($id)
	{
		return $this->response(Task\Task::where('creator_id', '=', $id)->with('Project')->with('Claimer')->get());    
	}

	// ------------------------------------------------------------------------
	public function users_claimed_tasks($id)
	{
		return $this->response(Task\Task::where('claimed_id', '=', $id)->with('Project')->with('Claimer')->get());    
	}

	// ------------------------------------------------------------------------
	public function users_un_claimed_tasks($id)
	{
		return $this->response(Task\Task::unClaimed()->where('creator_id', '=', $id)->with('Project')->with('Claimer')->get());    
	}

}
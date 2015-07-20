<?php

class APIController extends \BaseController {

	public function response($data)
	{
		return Input::get('pretty', false) ? Response::json($data, 200, [], JSON_PRETTY_PRINT) : Response::json($data);    
	}

	// ------------------------------------------------------------------------
	public function root_api()
	{
		return $this->response(['notice'=>'not a valid api endpoint']);    
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


	// ------------------------------------------------------------------------
	// Developer Create Client
	// ------------------------------------------------------------------------
	
	// ------------------------------------------------------------------------
	public function developer_page()
	{
		$clients = APIClient::where('user_id', '=', Auth::id())->get();
		return View::make('api.index', ['user'=>Auth::user(), 'endpoints'=>Config::get('api-endpoints'), 'clients'=>$clients]);
	}

	// ------------------------------------------------------------------------
	public function create_client()
	{
		$input = Input::all();
		$validator = Validator::make($input, APIClient::$rules);
		
		if($validator->fails()) {
			return $this->errorResponse($validator->errors()->all());
		}

		$client = new APIClient($input);
		$client->user_id = Auth::id();
		$client->save();
		return $this->statusResponse(['notice'=>'New API client created.', 'client'=>$client]);	
	}

}
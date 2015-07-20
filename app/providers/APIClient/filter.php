<?php namespace APIClient;

use Input;
use Response;
use Auth;

class ApiFilter {
 
	public function filter()
  	{	
  		if(Input::has('access_token') == false)
  		{
  			return $this->errorResponse('Missing access_token');
  		}
  		
    	if(!$this->validToken(Input::get('access_token'))){
			return Response::json(array('error' => 'Your access token is not valid'), 403);
    	}
      	
  	}

  	// ------------------------------------------------------------------------
  	public function errorResponse($error)
  	{
  		return Response::json(array('error' => $error), 403);
  	}

	// ------------------------------------------------------------------------
	public function validToken($token) 
	{
		return false;
	}
 
}
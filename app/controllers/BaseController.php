<?php

class BaseController extends Controller {

	public $wantsjson = false;
	
	protected function setupLayout()
	{	
		$this->wantsJson = Request::wantsJson() || (Input::has('json')&&Input::get('json')==true);

		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	// ------------------------------------------------------------------------
	public function errorResponse($message) {
		$wantsJson = Request::wantsJson();
		$backurl = URL::previous()."#form-message";
		return $wantsJson ? Response::json(['errors'=>$message], 400) : Redirect::to($backurl)->with(['errors'=>$message])->withInput();
	}
	

	// ------------------------------------------------------------------------
	public function statusResponse($message, $status=200, $withInput=true) {
		$backurl = isset($message['backurl']) ? $message['backurl'] : URL::previous();
		$wantsJson = $this->wantsJson;// isset($message['wantsjson'])? $message['wantsjson'] : Request::wantsJson();

		unset($message['wantsjson']);
		unset($message['backurl']);
		$message['status']=$status;

		return $wantsJson ? Response::json($message, $status) : ($withInput?Redirect::to($backurl)->with($message)->withInput():Redirect::to($backurl)->with($message));
	}

}

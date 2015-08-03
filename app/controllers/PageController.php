<?php

class PageController extends \BaseController {
	
	// ------------------------------------------------------------------------
	public function ChecksiteLogin() {
		if(Input::has('site-password')) {

			if(Input::get('site-password') == Config::get('config.site_password')) {
				$cookie = Cookie::forever('siteprotection', 'YES');
				return Redirect::back()->withCookie($cookie);
			}
		}
		Cookie::forget('siteprotection');
		return Redirect::back()->with(['errors'=>'Sorry wrong password for site.']);
	}

	// ------------------------------------------------------------------------
	public function index()
	{
		return View::make('site.admin.index');
	}

	// ------------------------------------------------------------------------
	public function feedback()
	{
		return View::make('site.popup.feeback-popup');
	}

	// ------------------------------------------------------------------------
	// Clean this up at some point
	// ------------------------------------------------------------------------
	public function store_feedback()
	{
		$admins 	= User::admin()->lists('email');
		$from 		= Auth::user();
		$feedback 	= Input::get('feedback');
		if(empty($feedback)) {
			return $this->statusResponse(['notice'=>'No feedback send']);			
		}
		$notice = new Notification(['event'=>Notification::NOTIFICATION_FEEDBACK]);
		$notice->user_id = Auth::id();
		$send_status = $notice->sendEmailToGroup($admins, null, array('from'=>$from, 'feedback'=>$feedback));

		return $this->statusResponse(['notice'=>'Thanks for the feedback', 'send_status'=>$send_status]);
	}

}















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
	public function store_feedback()
	{
		$admins = User::admin()->lists('email');
		$from = Auth::user();
		return ;
		/*
		$view = View::make($this->getViewPath(), array('task'=>$this->task))->render();
		$premailer = new \ScottRobertson\Premailer\Request();
		$response = $premailer->convert($view);
		$replyTo = $this->getReplyToAddress();
	
		Mail::send('emails.render', ['html'=>$response->downloadHtml()], function($message) use($admins, $from) {			
			$message->to($admins, 'Halp')->subject($subject?$subject:$this->getSubject());
			$message->replyTo($replyTo);
		});
		return true;
		*/
	}

}















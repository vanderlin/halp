<?php

class GoogleSessionController extends BaseController {

	// ------------------------------------------------------------------------
	static function getCreds() {
		
		$creds = null;
		$creds_file = [];
		$google_creds = Config::get('config.google');
		$env = Config::getEnvironment();
				

		$jsonfile = null;
		if($env == 'local' && $google_creds['oauth_local_path']) {
			$jsonfile = GoogleSessionController::loadCredentialsFile($google_creds['oauth_local_path']);
		}
		else if($google_creds['oauth_remote_path']) {
			$jsonfile = GoogleSessionController::loadCredentialsFile($google_creds['oauth_remote_path']);
		}

		if($jsonfile) {
			$google_creds = array_merge($google_creds, $jsonfile['web']);
		}

		//$creds_path   = Config::get('config.google_creds', 'remote');	
		//$creds 		= Config::get('config.google.'.$creds_path);
		//$obj 			= array_merge($google_creds);

		$obj = (object)$google_creds;
		return $obj;
	}

	// ------------------------------------------------------------------------
	private static function loadCredentialsFile($path) {
		$json_content = File::get($path);
		return $json_content ? json_decode($json_content, true) : [];
	}

	// ------------------------------------------------------------------------
	public static function getRedirectURI()
	{	
		$creds  = GoogleSessionController::getCreds();
		return array_key_exists('HTTP_HOST', $_SERVER) ? 'http://'.$_SERVER['HTTP_HOST'].'/oauth2callback' : $creds->redirect_uri;
	}

	// ------------------------------------------------------------------------
	public static function getClient() {
		
		$creds  = GoogleSessionController::getCreds();
		$client = new Google_Client();
		$env = Config::getEnvironment();

		

		if($env == 'local' && $creds->oauth_local_path) {
			$client->setAuthConfigFile($creds->oauth_local_path);
		}
		else if($creds->oauth_remote_path) {
			$client->setAuthConfigFile($creds->oauth_remote_path);
		}
		else {	
			$client->setApplicationName($creds->app_name);
			$client->setClientId($creds->client_id);
			$client->setClientSecret($creds->client_secret);
		}
		
		$client->setRedirectUri(GoogleSessionController::getRedirectURI()); 	

		$hd = Config::get('config.google.hd');
		if($hd) $client->setHostedDomain($hd);
		$client->setAccessType('offline');

		$client->addScope("https://www.googleapis.com/auth/userinfo.profile");
		$client->addScope("https://www.googleapis.com/auth/userinfo.email");
		$client->setScopes($creds->scopes);
		

		return $client;
	}

	// ------------------------------------------------------------------------
	public static function getState() {
		
		if(Session::has('state') == false) {
			$state = md5(rand());
			Session::put('state', $state);
		}
		return Session::get('state');
	}

	// ------------------------------------------------------------------------
	static function generateGoogleLoginURL($opt_options=array()) {
		
		$default_options = array('data-width'=>'standard', 'data-theme'=>'dark', 'data-callback'=>'signinCallback');
		$options = array_merge($default_options, $opt_options);
		$creds = GoogleSessionController::getCreds();

		$client = GoogleSessionController::getClient();
		
		$hd = Config::get('config.google.hd');
		if($hd) $client->setHostedDomain($hd);
		
		$client->setAccessType('offline');

		if(isset($options['state'])) $client->setState($options['state']);
		if(isset($opt_options['approval_prompt'])) $client->setApprovalPrompt($opt_options['approval_prompt']);

		$url = $client->createAuthUrl();

		return $url;
	}

	// ------------------------------------------------------------------------
	static public function getGoogleUser(&$user) {


		if($user->hasToken()) {
			$client = GoogleSessionController::getClient();
			$auth = new Google_Auth_OAuth2($client);
			$auth->refreshToken($user->getToken());
			$token = $auth->getAccessToken();
			
			

			$client->setAccessToken($auth->getAccessToken());
 			
 			$oauth2 = new Google_Service_Oauth2($client);
            $google_user = $oauth2->userinfo->get();
           

        	// save the latest token
        	// $user->google_token = $auth->getRefreshToken();
        	// $user->save();
           
			return $google_user;
		}
		else {
			$url = GoogleSessionController::generateGoogleLoginURL(['approval_prompt'=>'force', 'state'=>'refresh_token']);
			return Redirect::to($url);	

		}

		

		

	}

	// ------------------------------------------------------------------------
	static public function getOAuthOptions($opts = array()) {
		$creds = GoogleSessionController::getCreds();
		$other_opts = array();
		if(array_key_exists('hd', $creds)) $other_opts['hd'] = $creds->hd;
		if(is_array($opts)) {
			$other_opts = array_merge($other_opts, $opts);
		}
		return array_merge(Config::get('config.google.oauth_options'), $other_opts);
	}

	// ------------------------------------------------------------------------
	static function generateOAuthLink($opt_options=array(), $state=null) {
		
		$creds = GoogleSessionController::getCreds();
		$client = GoogleSessionController::getClient();

		if($creds->oauth_local_path) {
			$client->setAuthConfigFile($creds->oauth_local_path);
		}


		/*
		$client->setApplicationName($creds->app_name);
		$client->setClientId($creds->client_id);
		$client->setClientSecret($creds->client_secret);
		
		$client->setRedirectUri($creds->redirect_uri); 	// <--- huh?
		*/
		//$client->setRedirectUri('postmessage');				// <--- huh?


		
		if(array_key_exists('access_type', $creds->oauth_options)) {
			$client->setAccessType($creds->oauth_options['access_type']);
		}


		$url = $client->createAuthUrl();


		if(is_array($opt_options)===false) {
			$opt_options = (array)$opt_options;
		}
		$default_options = array();
		$options = array_merge($default_options, $opt_options);
		
		dd($options);

		if($state!=null) $options['state'] = $state;


		foreach ($options as $key => $value) {
			if($key == 'access_type') {
				$client->setAccessType($value);
			}
			else {
				if (is_bool($value)) {
					$value = $value?"true":"false";
				}
				$url .= '&'.$key.'='.$value;	
			}
		}

		
		return $url;
	}

	// ------------------------------------------------------------------------
	static function doesUserExistInEmails($emails) {
		foreach ($emails as $email) {
			$e = $email['value'];
			$u = User::findFromEmail($e);
			if($e != null) {
				return $u;
			}
		}
		return null;
	}

	// ------------------------------------------------------------------------
	public static function findUserFromGooglePerson($google) {
        return \User::where('id', '=', $google->id)->orWhere('email', '=', $google->email)->first();	
	}



	// ------------------------------------------------------------------------
	public function oauth2callback() 
	{
		if(Input::has('error')) {
			return Redirect::to('/')->withError(['error'=>'You need to allow google to signin']);
		}
		return $this->signin();	
	}


	// ------------------------------------------------------------------------
	public function linkAccountCallback() {
		
		$user = Auth::user();
		$code = Input::get('code');
		if($code && $user) {

			$wantsJson 	= Request::wantsJson();
			$creds 		= GoogleSessionController::getCreds();
			$client 	= GoogleSessionController::getClient();

			 // Exchange the OAuth 2.0 authorization code for user credentials.
	        $client->authenticate($code);
			$token = json_decode($client->getAccessToken());
			$attributes = $client->verifyIdToken($token->id_token, $creds->client_id)->getAttributes();


			$oauth2 = new \Google_Service_Oauth2($client);
			$google_user = $oauth2->userinfo->get();
			$email = $google_user->email;
			$username = explode("@", $email)[0];
			
			if(User::findFromEmail($google_user->email)) {
				return $wantsJson ? Response::json(['errors'=>'User already connected']) : Redirect::back()->with(['errors'=>'User already connected']);
			}
			



			// get google account info
			$user->google_token = json_encode($token);
			$user->google_id = $google_user->id;

			if(empty($user->firstname)) $user->firstname = $google_user->givenName;
			if(empty($user->lastname)) $user->lastname  = $google_user->familyName;

			if($user->hasDefaultProfileImage()) GoogleSessionController::saveGoogleProfileImage($google_user, $user);

			if($user->save()) {
				$back_url = 'users/'.$user->username;
				Auth::login($user);			
	        	return Redirect::to($back_url);	
			}
			else {
				return $wantsJson ? Response::json(['errors'=>$user->errors()->all()]) : Redirect::to('/')->with(['errors'=>$user->errors()->all()]);
			}


			// return Response::json(['errors'=>$user->givenName]);

		}
		
		return $wantsJson ? Response::json(['errors'=>'An error occurred']) : Redirect::to('/')->with(['errors'=>'An error occurred']);
	}

	// ------------------------------------------------------------------------
	public function linkAccount($id) {


		$user 		= User::find($id);
		$wantsJson 	= Request::wantsJson();
		$creds 		= GoogleSessionController::getCreds();
		$client 	= GoogleSessionController::getClient();

		if($user && $user->isMe()) {

 			$url = GoogleSessionController::generateOAuthLink(array_merge(['display'=>'page', 'prompt'=>'select_account'], GoogleSessionController::getOAuthOptions()), 'link');
 			
 			return Redirect::to($url);

		}
		else {
			$errors = ['errors'=>['No user found']];
			return $wantsJson ? Response::json($errors) : Redirect::to('/')->with($errors);
		}
		return $user;

	}

	// ------------------------------------------------------------------------
	public function unlinkAccount($id) {


		$user 		= User::find($id);
		$wantsJson 	= Request::wantsJson();

		if($user && $user->isMe()) {
	 		$user->google_token = "";
	 		$user->google_id = "";
	 		$user->save();
	 		return Redirect::back();	
		}
		else {
			$errors = ['errors'=>['No user found']];
			return $wantsJson ? Response::json($errors) : Redirect::to('/')->with($errors);
		}
	}
	
	// ------------------------------------------------------------------------
	public static function saveGoogleProfileImage(&$google_user, &$user) {

		// profile image
		//if(property_exists($google_user, 'picture')) {

			$image_url = $google_user->picture;
			
			if($image_url) {
		    	$image_url_parts = explode('?', $image_url);
		    	$image_url = $image_url_parts[0];
		    	$id = $user->id;
		    	
		    	$image_name =  $user->username.'_'.$id.'.jpg';
		    	
		    	

		    	if($user->profileImage && $user->profileImage->isShared()!=true) {
		    		$user->profileImage->removeOldFile();
		    		$user->profileImage->saveRemoteAsset($image_url, $image_name, ASSET::ASSET_TYPE_IMAGE);
		    		$user->profileImage->user()->associate($user);
		    	}
		    	else {
		    		$userImage = new Asset;
			    	$userImage->path = 'assets/content/users';
			    	$userImage->saveRemoteAsset($image_url, $image_name, ASSET::ASSET_TYPE_IMAGE);
			    	$userImage->save();
			    	$user->profileImage()->save($userImage);
			    	$user->profileImage->user()->associate($user);
		    	}
			}
		//}

	}

	// ------------------------------------------------------------------------
	public function getRefreshTokenFromClient(&$client) {
        // $oauth2 = new \Google_Auth_OAuth2($client);

        // if($oauth2) {
            
        //     $oauth2->refreshToken($this->google_token);
        //     $client->setAccessToken($oauth2->getAccessToken());

        //     $oauth2 = new \Google_Service_Oauth2($client);
        //     $google_user = $oauth2->userinfo->get();
            
        // }
	}


	// ------------------------------------------------------------------------
	public function createAccountFromGoogleAccount(&$google_user, $token) {

		$wantsJson 	= Request::wantsJson();
		$email 	  	= $google_user->email;
		$username 	= explode("@", $email)[0];

		$user 			 = new User;
	    $user->username  = $username;
	    $user->email 	 = $email;
		$password 		 = Hash::make($username); // <-- temp...

		$user->firstname = $google_user->givenName;
		$user->lastname  = $google_user->familyName;

		$user->password 			 = $password;
		$user->password_confirmation = $password;

		$user->confirmation_code 	 = md5($user->username.time('U'));
		$user->google_token 		 = $token;
		$user->google_id 			 = $google_user->id;

	    if($user->save()) {

			// profile image
			GoogleSessionController::saveGoogleProfileImage($google_user, $user);

			// Default Roles
        	if($username == 'tvanderlin' || $username == 'kmiller' || $username == 'Admin') { // <---
				$adminRole = Role::where('name', '=', 'Admin')->first();
				$user->attachRole($adminRole);
			}
			else {
	            $role = $role = Role::where('name', '=', 'Reader')->first();
	            if($role) {
	            	$user->attachRole($role);
	            	$user->save();
				}
			}

		
			$back_url = 'users/'.$username;
			Auth::login($user);			
        	return Redirect::to($back_url);		

		}
		else {
			return $wantsJson ? Response::json(['errors'=>$user->errors()->all()]) : Redirect::to('register')->with(['errors'=>$user->errors()->all()]);
		}
			
		
        return Response::json(['user'=>$user]);
	}

	// ------------------------------------------------------------------------
	public function signin() {
		
		$wantsJson 	= Request::wantsJson();
		$creds 		= GoogleSessionController::getCreds();
		$client 	= GoogleSessionController::getClient();
		$code 		= Input::get('code');

		if($code) {


			 // Exchange the OAuth 2.0 authorization code for user credentials.
	        $client->authenticate($code);
			$token = json_decode($client->getAccessToken());
			$attributes = $client->verifyIdToken($token->id_token, $creds->client_id)->getAttributes();

			$oauth2 = new \Google_Service_Oauth2($client);
			$google_user = $oauth2->userinfo->get();

			$email = $google_user->email;
			$username = explode("@", $email)[0];


			if($google_user) {

				$u = GoogleSessionController::findUserFromGooglePerson($google_user);
				$refreshToken = $client->getRefreshToken();


				// we have a user with this google+ account
				if($u != null) {

					// if(Input::has('state') && Input::get('state') == 'updated_profile') {
						$this->saveGoogleProfileImage($google_user, $u);						
					// }

					if(empty($u->google_token) && isset($refreshToken) ) {
						$u->google_token = $refreshToken;
						$u->save();
					}
					if(empty($u->google_id)) {
						$u->google_id = $google_user->id;
						$u->save();
					}
					if(empty($u->firstname) || empty($u->lastname)) {
						$u->firstname = $google_user->givenName;
						$u->lastname  = $google_user->familyName;
						$u->save();
					}

					Auth::login($u, true);
					$back_url = URL::to($u->getProfileURL());
					
					$resp = ['notice'=>'Welcome '.$u->username, 'back_url'=>$back_url];
					return $wantsJson ? Response::json($resp) : Redirect::to($back_url)->with(['notice'=>'Welcome '.$u->username]);
				}

				// we need to register this account
				else {

					if($client->getRefreshToken() == NULL) {
						$url = GoogleSessionController::generateGoogleLoginURL(['approval_prompt'=>'force', 'state'=>'refresh_token']);
						return Redirect::to($url);	
					}
					return $this->createAccountFromGoogleAccount($google_user, $client->getRefreshToken());
				}


				return $wantsJson ? Response::json(['error'=>'No user found with that id']) : Redirect::to('/')->with(['error'=>'No user found with that id']);

				
			}
			

			

			$errors = ['error'=>$email.' is not registered with '.Config::get('config.site-name')];
			return $wantsJson ? Response::json($errors) : Redirect::to('/')->with($errors);
		}
		
		return $wantsJson ? Response::json(['error'=>'Missing OAuth Code']) : Redirect::to('login')->with(['error'=>'Missing OAuth Code']);
	}

	// ------------------------------------------------------------------------
	/**
	 * Display a listing of the resource.
	 * GET /googlesession
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /googlesession/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /googlesession
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /googlesession/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /googlesession/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /googlesession/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /googlesession/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
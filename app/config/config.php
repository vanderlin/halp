<?php 


return array(

	'site_name'			     =>'Halp',
	'site_password'			 =>'ideobos',
	'carousel' 				 => array('interval'=>5000),		
	'api_base'			     => 'http://halp.ideo.com/api',
	'max_title'				 => 20,
	
	'google' => array(
	    'oauth_options'      => array('access_type'=>'offline', 'display'=>'popup'),
	    'oauth_local_path'   => 'assets/google/client_secret_386187553837-0jfjomr9dh4lfcp9gu6t0m6trhnu75st.apps.googleusercontent.com.json',
	    'oauth_remote_path'  => 'assets/google/client_secret_386187553837-r8nkcvifv9ckeeiml0gmdq1r646u4v03.apps.googleusercontent.com.json',
	    'api_key'            => "AIzaSyA_TYdDVr4vK84L7ixNncKz4t8HypMn2GY",
	    'server_key'		 => "AIzaSyA_TYdDVr4vK84L7ixNncKz4t8HypMn2GY",
	    'hd'                 => 'ideo.com',
	    'app_name'           => 'Halp',
	    'scopes'             => array("openid", "profile", "email"),
	    'google_url_options' => array()
	 ),

	
	

);

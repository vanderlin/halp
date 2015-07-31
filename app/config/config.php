<?php 


return array(

	'site_name'			      	=>'Halp',
	'site_password'			  	=>'ideobos',
	'api_base'			      	=> 'http://halp.ideo.com/api',
	'max_title'				  	=> 20,
	'site_url'				  	=> 'http://halp.ideo.com',

	'task_expiration_days'	  	=> 10,
	'task_expiration_soon_days' => 2,
	'active_task_per_page'	  	=> 16,
	'unclaimed_task_per_page' 	=> 8,
	


	'google' => array(
	    'oauth_options'       => array('access_type'=>'offline', 'display'=>'popup'),
	    'oauth_local_path'    => 'assets/google/client_secret_386187553837-0jfjomr9dh4lfcp9gu6t0m6trhnu75st.apps.googleusercontent.com.json',
	    'oauth_remote_path'   => 'assets/google/client_secret_386187553837-r8nkcvifv9ckeeiml0gmdq1r646u4v03.apps.googleusercontent.com.json',
	    'api_key'             => "AIzaSyA_TYdDVr4vK84L7ixNncKz4t8HypMn2GY",
	    'server_key'		  => "AIzaSyA_TYdDVr4vK84L7ixNncKz4t8HypMn2GY",
	    'hd'                  => 'ideo.com',
	    'app_name'            => 'Halp',
	    'scopes'              => array("openid", "profile", "email"),
	    'google_url_options'  => array()
	 ),

	
	

);

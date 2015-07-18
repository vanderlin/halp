<?php

return array(


// ------------------------------------------------------------------------
// Users
// ------------------------------------------------------------------------
array(
'name'=>'get_all_users',
'description'=>"Get a array of users using the Halp Application",
'method'=>'GET',
'url'=>'/api/users',
'example'=>'
[
   {
      id:1,
      username:"jsmith",
      email:"jsmith@ideo.com",
      firstname:"Jane",
      lastname:"Smith",
      google_id:"1273891264871624",
      created_at:"2013-09-15 12:02:53",
      updated_at:"2015-07-17 17:39:50",
      name:"Jane Smith",
      profile_image:"http://halp.ideo.com/images/31.jpg",
      profile_image_base:"http://halp.ideo.com/images/31",
      roles:[
         "Admin"
      ],
      url:"http://halp.ideo.com/users/jsmith"
   }
]
'),

// ------------------------------------------------------------------------
// User
// ------------------------------------------------------------------------
array(
'name'=>'get_user',
'description'=>"Get information about a user",
'method'=>'GET',
'url'=>'/api/users/{id}',
'example'=>'
{
  id:1,
  username:"jsmith",
  email:"jsmith@ideo.com",
  firstname:"Jane",
  lastname:"Smith",
  google_id:"1273891264871624",
  created_at:"2013-09-15 12:02:53",
  updated_at:"2015-07-17 17:39:50",
  name:"Jane Smith",
  profile_image:"http://halp.ideo.com/images/31.jpg",
  profile_image_base:"http://halp.ideo.com/images/31",
  roles:[
     "Admin"
  ],
  url:"http://halp.ideo.com/users/jsmith"
}
'),


// ------------------------------------------------------------------------
// User/Created Taks
// ------------------------------------------------------------------------
array(
'name'=>'get_users_created_tasks',
'description'=>"Get users created tasks",
'method'=>'GET',
'url'=>'/api/users/{id}/created_tasks',
'example'=>'
[
	{
	   id:2,
	   title:"Setup a wordpress site",
	   project_id:5,
	   creator_id:2,
	   duration:"few mins",
	   claimed_id:20,
	   claimed_at:{
	      date:"2015-07-21 17:32:16.000000",
	      timezone_type:3,
	      timezone:"UTC"
	   },
	   deleted_at:null,
	   created_at:"2015-07-17 17:32:16",
	   updated_at:"2015-07-17 17:32:16",
	   project:{
	      id:5,
	      title:"Rabbit",
	      user_id:12,
	      created_at:"2015-07-17 17:32:16",
	      updated_at:"2015-07-17 17:32:16"
	   },
	   claimer:null
	},
	...
]
'),


// ------------------------------------------------------------------------
// User/Claimed Taks
// ------------------------------------------------------------------------
array(
'name'=>'get_users_claimed_tasks',
'description'=>"Get tasks claimed by user",
'method'=>'GET',
'url'=>'/api/users/{id}/claimed_tasks',
'example'=>'
[
	{
	   id:2,
	   title:"Setup a wordpress site",
	   project_id:5,
	   creator_id:2,
	   duration:"few mins",
	   claimed_id:20,
	   claimed_at:{
	      date:"2015-07-21 17:32:16.000000",
	      timezone_type:3,
	      timezone:"UTC"
	   },
	   deleted_at:null,
	   created_at:"2015-07-17 17:32:16",
	   updated_at:"2015-07-17 17:32:16",
	   project:{
	      id:5,
	      title:"Rabbit",
	      user_id:12,
	      created_at:"2015-07-17 17:32:16",
	      updated_at:"2015-07-17 17:32:16"
	   },
	   claimer:{
	      id:20,
	      username:"barryfranecki",
	      email:"fake_tlabadie@hermiston.com",
	      firstname:"Raquel",
	      lastname:"Bosco",
	      google_id:"",
	      created_at:"2013-06-20 18:58:24",
	      updated_at:"2013-06-20 18:58:24",
	      name:"Raquel Bosco",
	      profile_image:"http://localhost:8888/images/18.jpg",
	      profile_image_base:"http://localhost:8888/images/18",
	      roles:[
	         "Writer"
	      ],
	      url:"http://localhost:8888/users/barryfranecki"
	   }
	},
	...
]
'),


// ------------------------------------------------------------------------
// User/ unClaimed Taks
// ------------------------------------------------------------------------
array(
'name'=>'get_users_un_claimed_tasks',
'description'=>"Get un claimed tasks creted by user",
'method'=>'GET',
'url'=>'/api/users/{id}/un_claimed_tasks',
'example'=>'
[
	{
	   id:2,
	   title:"Setup a wordpress site",
	   project_id:5,
	   creator_id:2,
	   duration:"few mins",
	   claimed_id:20,
	   claimed_at:{
	      date:"2015-07-21 17:32:16.000000",
	      timezone_type:3,
	      timezone:"UTC"
	   },
	   deleted_at:null,
	   created_at:"2015-07-17 17:32:16",
	   updated_at:"2015-07-17 17:32:16",
	   project:{
	      id:5,
	      title:"Rabbit",
	      user_id:12,
	      created_at:"2015-07-17 17:32:16",
	      updated_at:"2015-07-17 17:32:16"
	   },
	   claimer:null
	},
	...
]
'),



/*
array(
'description'=>"Get a list of all projects generated from tasks",
'method'=>'POST',
'url'=>'/projects',
'example'=>'
[
	{
		id: 1,
		title: "Bravo",
		user_id: 3,
		created_at: "2015-07-17 17:32:16",
		updated_at: "2015-07-17 17:32:16"
	},
]'),


array(
'description'=>"Get a list of all tasks",
'method'=>'POST',
'url'=>'/tasks',
'example'=>'
[
	{
		id: 1,
		title: "Bravo",
		user_id: 3,
		created_at: "2015-07-17 17:32:16",
		updated_at: "2015-07-17 17:32:16"
	},
]'),
*/










);
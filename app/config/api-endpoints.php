<?php

return array(


// ------------------------------------------------------------------------
// Users
// ------------------------------------------------------------------------
array(
'name'=>'get_all_users',
'description'=>"Get a array of users using the Halp Application",
'method'=>'POST',
'url'=>'/users',
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
'method'=>'POST',
'url'=>'/users/{id}',
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
'method'=>'POST',
'url'=>'/users/{id}/created_task',
'example'=>'
[
   {
      id:19,
      title:"Flying in space",
      project_id:5,
      creator_id:1,
      duration:"10 minutes",
      claimed_id:22,
      claimed_at:{
         date:"-0001-11-30 00:00:00.000000",
         timezone_type:3,
         timezone:"UTC"
      },
      deleted_at:null,
      created_at:"2015-07-17 18:00:19",
      updated_at:"2015-07-17 18:00:19"
   }
]
'),


// ------------------------------------------------------------------------
// User/Claimed Taks
// ------------------------------------------------------------------------
array(
'name'=>'get_users_claimed_tasks',
'description'=>"Get tasks claimed by user",
'method'=>'POST',
'url'=>'/users/{id}/claimed_task',
'example'=>'
[
   {
      id:19,
      title:"Flying in space",
      project_id:5,
      creator_id:1,
      duration:"10 minutes",
      claimed_id:22,
      claimed_at:{
         date:"-0001-11-30 00:00:00.000000",
         timezone_type:3,
         timezone:"UTC"
      },
      deleted_at:null,
      created_at:"2015-07-17 18:00:19",
      updated_at:"2015-07-17 18:00:19"
   }
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
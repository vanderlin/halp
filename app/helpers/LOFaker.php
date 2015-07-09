<?php 

use Notification\Notification;
use Carbon\Carbon;

/**
* LOFaker.php
*/
class LOFaker {
	
	public function locationable()
	{
		$locs = [];

		// spotables move them to locations
		Itinerary::withTrashed()->get()->each(function($item) use(&$locs) {
			
			foreach ($item->spots as $spot) {
				if($spot->location) {
					$vals =	[
						'location_id'=>$spot->location->id,
						'locationable_id'=>$item->id,
						'locationable_type'=>get_class($item),
						'created_at'=>$item->created_at,
						'updated_at'=>$item->created_at];

					$exist = DB::table('locationables')->where($vals)->first();
					
					if($exist == NULL) {
						DB::table('locationables')->insert($vals);	
						array_push($locs, $spot->location->id); 				
					}
				}
				
			}
		});

		$locations = Location::all()->each(function($item) use(&$locs) {
			$vals =	[
				'location_id'=>$item->id,
				'locationable_id'=>$item->locationable_id,
				'locationable_type'=>$item->locationable_type,
				'created_at'=>$item->created_at,
				'updated_at'=>$item->created_at
				];

			$exist = DB::table('locationables')->where($vals)->first();
			if($exist == NULL) {

				DB::table('locationables')->insert($vals);
				
				if($item->locationable_type == 'Spot') {
					$spot = Spot::withTrashed()->whereId($item->locationable_id)->first();
					if($spot) {
						$item->timestamps = false;
						$item->spot_id = $spot->id;
						$item->save();

						$spot->timestamps = false;
						$spot->location_id = $item->id;
						$spot->save();
					}
				}

				array_push($locs, $item->id);
			}

		});


		return $locs;

	}

	// ------------------------------------------------------------------------
	public function timestamp(Carbon $timestamp) 
	{
		return $timestamp;
	}
	public function findNotifications()
	{
		$notifications = [];

		$commentRepository = App::make('CommentRepository');

		// -------------------------------------
		Comment::withTrashed()->withSpot()->get()->each(function($item) use(&$notifications, &$commentRepository) {

			
			$spot = $item->spot()->withTrashed()->first();

			if($spot == NULL) {

				// remove this comment its dead
				$commentRepository->destroy($item->id);
				return;
			}

			if($spot->user !== null) {
				$from_user_id = $item->user_id;
				$to_user_id   = $spot->user_id;

				if($from_user_id !== $to_user_id) {

					$notification_event = array(
						'event'=> Notification::NOTIFICATION_USER_COMMENTED,
						'from_user_id'=>$from_user_id,
						'to_user_id'=>	$to_user_id,
						'parent'=> $item,
						'timestamp'=>$this->timestamp($item->created_at),
						'is_read'=>false,
					);

					array_push($notifications, $notification_event);
				}
			}

		});

		// -------------------------------------
		Visit::withTrashed()->get()->each(function($item) use(&$notifications) {

			
			$spot = $item->spot()->withTrashed()->first();

			if($spot == NULL) {

				// remove this comment its dead
				dd($spot);
				return;
			}

			if($spot->user !== null) {
				
				$from_user_id = $item->user_id;
				$to_user_id   = $spot->user_id;

				if($from_user_id !== $to_user_id) {

					$notification_event = array(
						'event'=> Notification::NOTIFICATION_USER_VISITED,
						'from_user_id'=>$from_user_id,
						'to_user_id'=>	$to_user_id,
						'parent'=> $item,
						'timestamp'=>$this->timestamp($item->created_at),
						'is_read'=>false,
					);

					array_push($notifications, $notification_event);
				}
			}

		});


		// -------------------------------------
		Illuminate\Support\Collection::make(DB::table('userables')->get())->each(function($item) use(&$notifications) {

			$type = $item->userable_type;
			$parent = $type::withTrashed()->whereId($item->userable_id)->first();

			$notification_event = array(
				'event'=> Notification::NOTIFICATION_USER_ITINERARY_SHARED,
				'to_user_id'=>	$item->user_id,
				'from_user_id'=>$parent->user->id,
				'parent'=> $parent,
				'timestamp'=>$this->timestamp(new Carbon($item->created_at)),
				'is_read'=>false,
			);
			array_push($notifications, $notification_event);
		});	

		// -------------------------------------
		User::all()->each(function($user) use(&$notifications) {
			

			// welcome message
			$notification_event = array(
				'event'=> Notification::NOTIFICATION_USER_WELCOME,
				'to_user_id'=>$user->id,
				'from_user_id'=>null,
				'parent'=> $user,
				'timestamp'=>$this->timestamp($user->created_at),
				'is_read'=>false,
			);
			array_push($notifications, $notification_event);




			// when someone added your spot to favs
			foreach($user->favorites->getLocations() as $location) {
				if($location->hasSpot() && $location->spot->user_id !== $user->id) {
					
					// send to the owner of the spot
					$to_user_id = $location->spot->user_id;

					// who is doing this...?
					$from_user_id = $user->id;


					// welcome message
					$notification_event = array(
						'event'=> Notification::NOTIFICATION_USER_FAVORITED,
						'to_user_id'=>$to_user_id,
						'from_user_id'=>$from_user_id,
						'parent'=> $location->spot,
						'timestamp'=>$this->timestamp($location->pivot->created_at),
						'is_read'=>false,
					);
					array_push($notifications, $notification_event);
				}
			}
			

		});
	
		

		foreach ($notifications as $notice) {
			Notification::fireNotification($notice);
		}

		return $notifications;
	}


	// ------------------------------------------------------------------------
	public function createTimeBinsForActivity() 
	{
		$g = [];
		$activities = Activity::orderBy('created_at', 'desc')->get();
		
		$prevRow = null;
		$lastGroup = array();

		foreach ($activities as &$row) {
			
			if(count($g) == 0) {
				$uid = "$row->user_id"."_".$row->created_at->getTimestamp();
				$g[$uid] = array($row);
			}
			else {
				$group_to_add_to = -1;
				$group_index = 0;

				// we only want to search to group acts together 
				// if the activity is of the same type of event
				// but not a 'new spot'
				// and is a close enough time period (3 mins) 
				// and from the same user
				if( $row->event !== 'activity.user.commented' &&
					$row->event !== 'activity.spot.created') 
				{
						foreach ($g as $key=>&$group) {
							foreach ($group as &$act) {
								
								if( $row->user_id == $act->user_id && 
									$row->created_at->diffInMinutes($act->created_at) <= 30 &&
									$row->event == $act->event) {
										$group_to_add_to = $key;
										break;
								}	
							}
							$group_index ++;
						}
				}

				if($group_to_add_to == -1) {
					$uid = "$row->user_id"."_".$row->created_at->getTimestamp();
					$g[$uid] = array($row);
				}
				else {

					array_push($g[$group_to_add_to], $row);	
				}

			}

			
		}
		
		
		foreach ($g as $key=>&$group) {
			foreach ($group as &$act) {
				$act->timestamps = false;
				$act->event_id = $key;
				$act->save();
		
			}
		}

      	return new Collection($g);
	}

	// ------------------------------------------------------------------------
	public function seedBlogPost()
	{
		
		$blogRepo = App::make('BlogRepository');
		$faker = Faker\Factory::create();

		foreach ($blogRepo->getAllPost()->get() as $post) {
			$blogRepo->deleteBlogPost($post->id);
		}
		
		$user = User::whereHas('roles', function($q) {
			$q->whereName('Editor');
		})->orderBy(DB::raw('RAND()'))->first();

		$tags = Tag::orderBy(DB::raw('RAND()'))->take(rand(2, 4))->lists('id');
		

		$body = "";
		foreach ($faker->paragraphs(rand(5,10)) as $p) {
			$body .= $p."\n";
		}
		
		$nPosts = 10;
		$posts = [];
		for ($i=0; $i < $nPosts; $i++) { 
			
			$post = [
				'title'=>$faker->sentence(rand(2,5)),
				'body'=>$body,
				'user'=>$user,
				'hero_file_url'=>$faker->imageUrl(1024, 768, 'food'),
				'tags'=>$tags,
				'status'=>'Publish',
				'post_type'=>$blogRepo->getPostType('post')->id,
				'created_at'=>$faker->dateTimeBetween('-3 years', 'now')
				];

			if(rand(0,10)>5) {
				$post['excerpt'] = $faker->paragraph(rand(1,3));
			}		
			
			$post = $blogRepo->createBlogPost($post);
			
			array_push($posts, $post);
		}
		return $posts;
	}

	// ------------------------------------------------------------------------
	public function debugSize()
	{
		$data = [];
		$spots = Spot::getRecent(100000);
		foreach ($spots as $spot) {
			$asset = $spot->getThumbnailImage();
			$info = get_image_info($asset->relativeURL());

			array_push($data, $info);
		}
		return $data;
	}

	// ------------------------------------------------------------------------
	public function createSlugsForAllSpots() 
	{

		foreach (Spot::orderBy('name', 'desc')->get() as $spot) {
			$spot->timestamps = false;
			$spot->slug = NULL;
			$spot->save();
		}

		$slugs = [];
		foreach (Spot::orderBy('name', 'desc')->get() as $spot) {
			array_push($slugs, $spot->slug);
		}
		return $slugs;
	}

	// ------------------------------------------------------------------------
	public function addTimestampsToRolesTable() 
	{
		$q = User::all()->each(function($user) {
			$roles = $user->roles;
			$dt = $user->created_at->toDateTimeString();
			foreach ($roles as $role) {
		
				if($role==null) dd($user);

				$r = DB::table('assigned_roles')
						->where('user_id', '=', $user->id)
						->where('role_id', '=', $role->id)
						->first();

				if($r && $r->created_at == '0000-00-00 00:00:00') {
					DB::table('assigned_roles')
				    	->where('id', $r->id)
				        ->update(array('created_at' => $dt, 'updated_at' => $dt));	
				}
			}

		});
		return $q;
	}

	// ------------------------------------------------------------------------
	public function addTimestampToBlankSpotablesItineraries() 
	{
		$query = DB::table('spotables')->where('spotable_type', '=', 'Itinerary')->get();

		foreach ($query as $row) {

			if($row->created_at == '0000-00-00 00:00:00') {
				$spot = Spot::find($row->spot_id);
				$itin = Itinerary::find($row->spotable_id);
				if($itin && $itin->user) {
					$user = $itin->user;
					$new_date = $user->created_at->toDateTimeString();

					DB::table('spotables')
			          ->where('id', $row->id)
			          ->update(array('created_at' => $new_date, 'updated_at' => $new_date));
		      	}
		      	else {
		      		dd($itin);
		      	}
			}
		}
		return $query;
	}

	// ------------------------------------------------------------------------
	public function findEmptyCommentsVisitAssociated()
	{
		
		$visits_to_make = [];

		$comments = Comment::withTrashed()
		->whereBody('')
		->where('commentable_type', '=', 'Spot')
		->orWhereNull('body')
		->get()
		->each(function($item) use(&$visits_to_make) {
			
			// do we have a visit ?
			$visit = Visit::withTrashed()
			->where('user_id', '=', $item->user_id)
			->where('spot_id', '=', $item->commentable_id)
			->get();

			if($visit->count()==0) {
				array_push($visits_to_make, (object)['user_id'=>$item->user_id, 'spot_id'=>$item->commentable_id, 'timestamp'=>$item->created_at]);
			}


		});

		foreach ($visits_to_make as $make) {
			$user = User::find($make->user_id);
			$spot = Spot::find($make->spot_id);
			if($user && $spot) {
				$visit = new Visit;
				$visit->timestamps = false;
				$visit->user()->associate($user);
				$visit->spot()->associate($spot);
				$visit->created_at = $visit->updated_at = $make->timestamp;
				$visit->save();
				$visit->timestamps = true;

				$visit->fireActivityEvent();
			}
		}
		
		$comments->each(function($item) {
			$item->forceDelete();
		});

		return [$visits_to_make, $comments];


	}

	// ------------------------------------------------------------------------
	public function createFakeUser()
	{
		
		$random_user_data = (object)json_decode(get_remote_file('http://api.randomuser.me/'));
		$random_user_data = $random_user_data->results[0]->user;
		$faker = Faker\Factory::create();

		$role = $role = Role::where('name', '=', 'Writer')->first();
		$joinDate = $faker->dateTimeBetween('-3 years', 'now');
		
		$user 			  = new User;
		$user->timestamps = false;
	    $user->username   = $random_user_data->username;
	    $user->email 	  = $random_user_data->email;
		$password 		  = Hash::make($random_user_data->username);
		$user->firstname  = $random_user_data->name->first;
		$user->lastname   			 = $random_user_data->name->last;
		$user->password 			 = $password;
		$user->password_confirmation = $password;
		$user->confirmed 			 = 1;
		$user->confirmation_code 	 = md5($user->username.time('U'));
		$user->created_at = $user->updated_at = $joinDate;
		$user->save();

		$image_url = $random_user_data->picture->large;
		$userImage = new Asset;
		$userImage->path = 'assets/content/users';
		$userImage->saveRemoteAsset($image_url,  $user->username.".jpg", Asset::ASSET_TYPE_IMAGE);
		$userImage->save();
		$user->profileImage()->save($userImage);
		$user->profileImage->user()->associate($user);
		$user->save();
        $user->attachRole($role);
		$user->save();
		return $user;
	}


	// ------------------------------------------------------------------------
	public function itinerairesSlugs() 
	{
		$i = Itinerary::withTrashed()->get()->each(function($item) {

			$item->slug;

		});
		return $i;
	}

	// ------------------------------------------------------------------------
	public function createPastActivityEvents() 
	{
		

		$log_file_path = '../app/storage/logs/laravel.log';

		// clear out the log file
		File::put($log_file_path, '');
	
		$feed = App::make('ActivityRepository');
		$feed = $feed->getActivityHistory();
		
		foreach ($feed as $activity) {
			Event::fire($activity->name, array($activity));
		}

		return $feed;

		return nl2br(File::get($log_file_path));
	}

	// ------------------------------------------------------------------------
	public function checkForMissingActivity() 
	{

		$feed = App::make('ActivityRepository');
		$feed = $feed->getActivityHistory(true, 5000, 10000);
		


		// $date  = clone $event->timestamp;
		// $start = clone $date; $start->addMinutes(-$this->timeframe);
		// $end   = clone $date; $end->addMinutes($this->timeframe);
		$missing 	   = [];
		echo "<table cellpadding='5'>";
		foreach ($feed as $activity) {

			$activity_type = get_class($activity->parent);
			$activity_id   = $activity->parent->id;
			$user_id 	   = $activity->user_id;

			$acts = Activity::withTrashed()
						 ->where('activity_type', '=', $activity_type)
						 ->where('activity_id', '=', $activity_id)
						 ->where('user_id', '=', $user_id)
						 ->where('event', '=', $activity->name)
						 ->get();
			echo "<tr>";						 
			echo "<td>{$activity->name}</td> <td>{$activity_type}:{$activity_id}</td>" ;
			if($acts->count()==0) {
				


				echo "<td style='color:red'>Missing {$activity->timestamp}";

					if (Input::has('fire') && Input::get('fire')==true) {
						Event::fire($activity->name, array($activity));
						echo "<b> *** Event Fired *** </b>";
					}
					
				echo "</td>";
			}
			else {
				echo "<td style='color:green'>Found {$acts->first()->id}</td>";
			}

			echo "</tr>";
		}
		echo "</table>";
		return;
	 	return $missing;

	}

	// ------------------------------------------------------------------------
	public function updateAllSpotsToSoftDelete() 
	{
		
		echo "Total before soft delete: ".Spot::all()->count()."<br>";
		foreach (Spot::withTrashed()->get() as $spot) {
			if($spot->status == 'Delete') {
				$spot->delete(); 
			}
		}
		echo "Total after soft delete: ".Spot::all()->count()."<br>";
	}

	// ------------------------------------------------------------------------
	public function findDeadActivity()
	{
		// 		{
		// id: "1",
		// event: "activity.user.favorited",
		// activity_type: "Spot",
		// activity_id: "299",
		// user_id: "60",
		// deleted_at: null,
		// created_at: "2015-02-04 18:34:33",
		// updated_at: "2015-02-04 18:34:33",
		// event_id: "54d2699e5b1ee_60_1423074873"
		// },

		echo "<table cellpadding='5'>";
		$activities   = Activity::all();
		$total_dead = 0;
		$activities->each(function($item) use(&$total_dead) {
			if($item->parent == NULL) {
				$total_dead ++;
			}
		});
		echo "<tr style='font-weight:bold'><td>Total Dead:</td><td>{$total_dead}</td></tr>";
		
		$activities->each(function($item) {

			echo "<tr>";
				echo "<td>{$item->event}</td>";
				echo "<td>{$item->activity_type}:{$item->activity_id}</td>";

				$parent = $item->parent;

				if($parent !== NULL && $item->event == 'activity.itinerary.created' && $parent->isFavorites()) {
					echo "<td style='color:red'>*** Favorites Itinerary in Feed ***</td>";	
					if (Input::has('murder') && Input::get('murder')==true) {
						$item->forceDelete();
					}
				}
				if($parent == NULL) {
					echo "<td style='color:red'>*** Dead Parent ***</td>";	
					if (Input::has('fire') && Input::get('fire')==true) {
						$item->delete();
						echo "<td style='color:red'><b>*** Removed ***</b></td>";					
					}				
				}
				



			echo "</tr>";
			
		});
		echo "</table>";

		// return $activities;
		
	}

	// ------------------------------------------------------------------------
	public function getUserNotTakenTour()
	{	
		$resp = [];
		foreach (User::all() as $user) {
			if($user->didTakeTour() === false) {
				array_push($resp, $user);
			}
		}
		return $resp;
	}


}
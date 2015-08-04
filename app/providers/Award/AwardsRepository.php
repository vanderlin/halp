<?php namespace Award;

use Carbon\Carbon;
use DB;
use \Illuminate\Support\Collection as Collection;
use Input;
use Paginator;
use User;
use Str;
use Validator;
use Config;
use Project;
use Notification;

/**
* Awards Repository
*/

class AwardsRepository  {
	
	private $listener;

	// ------------------------------------------------------------------------
	public function __construct() {
		
	}

	// ------------------------------------------------------------------------
	public function setListener($listener) {
		$this->listener = $listener;
	}


	// ------------------------------------------------------------------------
	public function log($message)
	{
		$this->listener->statusResponse($message);			
	}

	// ------------------------------------------------------------------------
	public function checkForAwards($week_of=null)
	{
		
		$now = $week_of ? $week_of : Carbon::now()->setTimeZone('America/New_York');

		$awards = Award::getAwards();
		foreach ($awards as $award) {
			
			$type = $award->name;
			$freq = Award::frequencyForType($type);

			if(	$freq == Award::AWARD_FREQ_WEEK && 
				$now->dayOfWeek == Config::get('awards.award_week_day') &&
				$now->hour >= Config::get('awards.award_week_hour')) {
				
				$this->log("-[$type] query...");
				$quary_award = Award::awardsForWeek($type, $now)->first();

				if($quary_award == NULL) {

					$results = ['type'=>$type];
					$new_award = NULL;

					// -------------------------------------
					if($type == Award::AWARD_MOST_TASK_CLAIMED_WEEK) {
						$top_user_created_tasks = User::orderByCreatedTasks()->first();
						$new_award = $this->store(['user_id'=>$top_user_created_tasks->id, 'name'=>$type], true);
						
						
						array_push($results, $new_award);
					}

					// -------------------------------------
					else if($type == Award::AWARD_MOST_TASK_CREATED_WEEK) {
						$top_user_claimed_this_week = User::mostHelpfulForWeek()->first();
						$new_award = $this->store(['user_id'=>$top_user_claimed_this_week->id, 'name'=>$type], true);
						array_push($results, $new_award);
					}

					// -------------------------------------
					else if($type == Award::AWARD_MOST_ACTIVE_PROJECT_WEEK) {
						$top_active_project = Project::orderByMostTasks()->with('user')->first();
						$new_award = $this->store(['user_id'=>$top_active_project->user->id, 'project_id'=>$top_active_project->id, 'name'=>$type], true);
						array_push($results, $new_award);

					}

					// alter the dates
					if($week_of != NULL && $new_award) {
						$new_award->created_at = $new_award->updated_at = $now;
						$new_award->save();
					}
							
					$this->log($results);
				}
				else {
					$this->log("\t[$type] exist Award::($quary_award->id)");	
				}
			}

			

		}

	}

	// ------------------------------------------------------------------------
	public function checkAwardForUser($user)
	{

		$awards = Award::getAwards();
		$this->log(['user'=>$user->getName(), 'createdTasks'=>$user->createdTasks->count(), 'claimedTasks'=>$user->claimedTasks->count()]);
		foreach ($awards as $award) {
			
			$type = $award->name;
			$freq = Award::frequencyForType($type);

			// -------------------------------------
			if($user->hasAward($type) == false && $freq == Award::AWARD_FREQ_ONCE)
			{

				$this->log("running [$type] query...");
				$results = [];
			
				// -------------------------------------
				if($type == Award::AWARD_CLAIMED_5 && $user->claimedTasks->count() >=5 ) 
				{
					array_push($results, $this->store(['user_id'=>$user->id, 'name'=>$type], true));
				}

				// -------------------------------------
				if($type == Award::AWARD_CLAIMED_10 && $user->claimedTasks->count() >=10 ) 
				{
					array_push($results, $this->store(['user_id'=>$user->id, 'name'=>$type], true));
				}

				// -------------------------------------
				if($type == Award::AWARD_CLAIMED_25 && $user->claimedTasks->count() >=25 ) 
				{
					array_push($results, $this->store(['user_id'=>$user->id, 'name'=>$type], true));
				}

				// -------------------------------------
				if($type == Award::AWARD_CLAIMED_50 && $user->claimedTasks->count() >=50 ) 
				{
					array_push($results, $this->store(['user_id'=>$user->id, 'name'=>$type], true));
				}

				// -------------------------------------
				if($type == Award::AWARD_CLAIMED_75 && $user->claimedTasks->count() >=75 ) 
				{
					array_push($results, $this->store(['user_id'=>$user->id, 'name'=>$type], true));
				}

				// -------------------------------------
				if($type == Award::AWARD_CLAIMED_100 && $user->claimedTasks->count() >=100 ) 
				{
					array_push($results, $this->store(['user_id'=>$user->id, 'name'=>$type]));
				}
				if(empty($results)) {
					$this->log("*** User Not Eligible ***");
				}
				else {
					$this->log($results);
				}
			}
			else {
				// $this->log("*** user_id({$user->id}) $type Found ***");
			}
					
		

		}
		
	}
	
	// ------------------------------------------------------------------------
	public function find($id) {
		
		if(is_object($id)) {
			$id = $id->id;
		}
		$award = Award::withTrashed()->whereId($id)->first();
		return $this->listener->statusResponse(['award'=>$award]);		
	}

	// ------------------------------------------------------------------------
	public function store($input, $return=false)
	{		
		$validator = Validator::make($input, Award::$rules);
		if($validator->fails()) {
			return $this->listener ? $this->listener->errorResponse($validator->errors()->all()) : $validator->errors()->all();
		}

		$award = new Award($input);
		$award->save();

		Notification::fire($award, Notification::NOTIFICATION_NEW_AWARD);

		return $return ? $award : $this->listener->statusResponse(['notice'=>'Award Created', 'award'=>$award]);		
	}

	// ------------------------------------------------------------------------
	public function delete($id) 
	{
		if(is_object($id)) {
			$id = $id->id;
		}
		$award = Award::withTrashed()->whereId($id)->first();
		if($award) 
		{
			$award->delete();
		}
		return $this->listener->statusResponse(['award'=>$award]);
	}
}
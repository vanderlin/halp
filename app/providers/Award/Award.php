<?php namespace Award;

use BaseModel;
use User;
use Validator;
use Carbon;
use Config;
use DB;

class Award extends BaseModel {

	protected $fillable  = ['user_id', 'name', 'task_id', 'project_id', 'notification_id'];
	public static $rules = ['user_id'=>'required', 'name'=>'required'];
	
	const AWARD_CLAIMED_5   = "award.claimed.5.once";
	const AWARD_CLAIMED_10  = "award.claimed.10.once";
	const AWARD_CLAIMED_25  = "award.claimed.25.once";
	const AWARD_CLAIMED_50  = "award.claimed.50.once";
	const AWARD_CLAIMED_75  = "award.claimed.75.once";
	const AWARD_CLAIMED_100 = "award.claimed.100.once";

	const AWARD_MOST_TASK_CLAIMED_WEEK = "award.most.tasks.claimed.week";
	const AWARD_MOST_TASK_CREATED_WEEK = "award.most.tasks.created.week";
	const AWARD_MOST_ACTIVE_PROJECT_WEEK = "award.most.active.project.week";

	const AWARD_FREQ_DAY   = "award.freq.day";
	const AWARD_FREQ_WEEK  = "award.freq.week";
	const AWARD_FREQ_MONTH = "award.freq.month";
	const AWARD_FREQ_YEAR  = "award.freq.year";
	const AWARD_FREQ_ONCE  = "award.freq.once";

    // ------------------------------------------------------------------------
    public function toArray() 
    {
    	$array = parent::toArray();
     	return $array;
    }

    // ------------------------------------------------------------------------
    public static function getAwards()
    {	
    	$awards = Config::get('awards.types');
    	foreach ($awards as &$award) {
    		$award = array_to_object($award);
    	}
    	return $awards;
    }

    // ------------------------------------------------------------------------
    public static function getOnceAwards()
    {	
    	$awards = [];
    	foreach (Award::getAwards() as $type) {
    		$type =(object)$type;
    		if(Award::isOnce($type->name))
    		{
    			array_push($awards, $type);
    		}
    	}
    	return $awards;
    }

    // ------------------------------------------------------------------------
    public static function isOnce($type)
    {
    	return stripos($type, "once") !== false;
    }

    // ------------------------------------------------------------------------
    public static function getAwardType($type)
    {
    	$award = NULL;
		foreach(Config::get('awards.types') as $award_type) {
			if($award_type['name'] == $type) {
				$award = $award_type;
			}
		}
		return (object)$award;
	}

    // ------------------------------------------------------------------------
    public static function imageForType($type)
    {
    	return Award::getAwardType($type)->image;
    }

    // ------------------------------------------------------------------------
    public static function frequencyForType($type)
    {
    	switch($type) {
    		case Award::AWARD_CLAIMED_5:
			case Award::AWARD_CLAIMED_10:
			case Award::AWARD_CLAIMED_25:
			case Award::AWARD_CLAIMED_50:
			case Award::AWARD_CLAIMED_75:
			case Award::AWARD_CLAIMED_100:
			return Award::AWARD_FREQ_ONCE;
			break;

			case Award::AWARD_MOST_TASK_CLAIMED_WEEK:
			case Award::AWARD_MOST_ACTIVE_PROJECT_WEEK:
			case Award::AWARD_MOST_TASK_CREATED_WEEK:
			return Award::AWARD_FREQ_WEEK;
			break;
		}
    }


    // ------------------------------------------------------------------------
    public function scopeAwardsForWeek($query, $type, $week_of=null)
    {
        /*
        SELECT 
        awards.*
        FROM awards
        WHERE 
        awards.name = 'award.most.tasks.claimed.week' 
        AND
        YEARWEEK(awards.created_at) = YEARWEEK('2015-08-12')
        */
        if($week_of == null) $week_of = Carbon\Carbon::now();
        $date_str = $week_of->toDateString();

        return $query->whereRaw(DB::raw("name = '$type'"))  
                     ->whereRaw(DB::raw("YEARWEEK(created_at) = YEARWEEK('$date_str')")); 
    }

    // ------------------------------------------------------------------------
    public function canHaveMany($type)
    {
    	switch($type) {
    		case Award::AWARD_CLAIMED_5:
			case Award::AWARD_CLAIMED_10:
			case Award::AWARD_CLAIMED_25:
			case Award::AWARD_CLAIMED_50:
			case Award::AWARD_CLAIMED_75:
			case Award::AWARD_CLAIMED_100:
			return false;
			break;

			case Award::AWARD_MOST_TASK_CLAIMED_WEEK:
			case Award::AWARD_MOST_ACTIVE_PROJECT_WEEK:
			case Award::AWARD_MOST_TASK_CREATED_WEEK:
			return true;
			break;
		}
    }

    // ------------------------------------------------------------------------
    public function user()
    {
        return $this->belongsTo('User');
    }

	// ------------------------------------------------------------------------
	public function save(array $options = array()) 
	{
  		parent::save();
	}

}
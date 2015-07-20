<?php namespace Task;

use BaseModel;
use SoftDeletingTrait;
use User;
use Validator;
use Carbon;
use DB;
use URL;
use Auth;

class Task extends BaseModel {
	
	use SoftDeletingTrait;

	protected $fillable  = ['title', 'project_id', 'creator_id', 'claimed_id', 'duration'];
	protected $dates     = ['deleted_at'];
	public static $rules = ['title'=>'required', 'duration'=>'required', 'project'=>'required'];
	

    // ------------------------------------------------------------------------
    public function toArray() 
    {
    	$array = parent::toArray();
     	return $array;
    }

    // ------------------------------------------------------------------------
    public function scopeClaimed($query)
    {
    	return $query->whereNotNull('claimed_id')->orderBy('created_at', 'DESC');
    }

    // ------------------------------------------------------------------------
    public function scopeMostClaimed($query)
    {
    	return $query->whereNotNull('claimed_id')
                     ->groupBy('claimed_id')
                     ->select(array('*', DB::raw('count(*) as claimed_count')))
                     ->orderBy('claimed_count', 'DESC');
    }

    // ------------------------------------------------------------------------
    public function scopeUnClaimed($query)
    {
    	return $query->whereNull('claimed_id')->orderBy('created_at', 'DESC');
    }

	// ------------------------------------------------------------------------
	public function save(array $options = array()) 
	{
  		parent::save();
	}

	// ------------------------------------------------------------------------
	public function getURL($relative=true) 
	{
		return URL::to('tasks/'.$this->id); 
	}

	// ------------------------------------------------------------------------
	public function getClaimURL($relative=true) 
	{
		return URL::to('/?claim_task='.$this->id); 
	}

	// ------------------------------------------------------------------------
	public function isMine()
	{
		return Auth::check() && Auth::id() == $this->creator_id;
	}
	// ------------------------------------------------------------------------
	public function creator()
	{
		return $this->belongsTo('User');
	}

		// ------------------------------------------------------------------------
	public function claimer()
	{
		return $this->belongsTo('User', 'claimed_id');
	}


	// ------------------------------------------------------------------------
	public function notification()
	{
		return $this->hasOne('Notification');
	}

	// ------------------------------------------------------------------------
	public function getIsClaimedAttribute()
	{
		return $this->claimed_id != NULL;
	}
	
	// ------------------------------------------------------------------------
	public function getClaimedAtAttribute($val)
	{
		return new Carbon\Carbon($val);
	}
	// ------------------------------------------------------------------------
	public function project()
	{
		return $this->belongsTo('Project\Project');
	}

	// ------------------------------------------------------------------------
	public function delete() {
      	$notification = $this->notification;
      	if($notification)
      	{
      		$notification->delete();
      	}
    	parent::delete();
    }

}	
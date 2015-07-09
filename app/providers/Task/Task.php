<?php namespace Task;

use BaseModel;
use SoftDeletingTrait;
use User;
use Validator;
use Carbon;

class Task extends BaseModel {
	
	use SoftDeletingTrait;

	protected $fillable  = ['title', 'project_id', 'creator_id', 'claimed_id'];
	protected $dates     = ['deleted_at'];
	public static $rules = [];
	

    // ------------------------------------------------------------------------
    public function toArray() 
    {
    	$array = parent::toArray();
     	return $array;
    }

	// ------------------------------------------------------------------------
	public function save(array $options = array()) 
	{
  		parent::save();
	}

	// ------------------------------------------------------------------------
	public function creator()
	{
		return $this->belongsTo('User');
	}

	// ------------------------------------------------------------------------
	public function claimer()
	{
		return $this->belongsTo('claimed_id');
	}

	// ------------------------------------------------------------------------
	public function getIsClaimedAttribute()
	{
		return $this->claimed_id != NULL;
	}
	// ------------------------------------------------------------------------
	public function project()
	{
		return $this->belongsTo('Project\Project');
	}
}	
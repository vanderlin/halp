<?php namespace Project;

use BaseModel;
use User;
use Validator;
use Carbon;
use URL;
class Project extends BaseModel {
	
	protected $fillable  = ['title', 'user_id'];
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
	public function getURL($relative=true)
	{
		return URL::to('projects/'.$this->id);
	}
}
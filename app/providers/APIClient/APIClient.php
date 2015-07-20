<?php

use BaseModel;
use User;
use Validator;
use Carbon;

class APIClient extends BaseModel {
	
	protected $table 	 = 'api_clients';
	protected $fillable  = [];
	protected $dates     = ['deleted_at'];
	public static $rules = ['name'=>'required'];
	

    // ------------------------------------------------------------------------
    public function __construct($attributes = array(), $exists = false) {
    	parent::__construct($attributes, $exists);
     	dd($this->uid);
     	$this->uid = md5(uniqid(rand(), true));
    }

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

}
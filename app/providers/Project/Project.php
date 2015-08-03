<?php namespace Project;

use BaseModel;
use User;
use Validator;
use Carbon;
use URL;
use DB;

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
    public function scopeOrderByMostTasks($query)
    {
    	/*
    	SELECT 
		projects.*,
		COUNT(tasks.project_id) AS total_tasks
		FROM projects AS projects
		LEFT JOIN tasks
		ON projects.id = tasks.project_id AND tasks.claimed_id IS NOT NULL  
		GROUP BY projects.id
		ORDER BY total_tasks DESC
		*/
        return $query->select(array('projects.*', DB::raw("COUNT(tasks.id) as total_tasks")))
             ->join('tasks', function($join) {
                $join->on('projects.id', '=', 'tasks.project_id');
             })  
             ->groupBy("projects.id")
             ->orderBy("total_tasks", 'DESC');
    }

	// ------------------------------------------------------------------------
	public function save(array $options = array()) 
	{
  		parent::save();
	}

	// ------------------------------------------------------------------------
	public function user() 
	{
  		return $this->belongsTo('User');
	}

	// ------------------------------------------------------------------------
	public function tasks() 
	{
  		return $this->hasMany('Task\Task');
	}
	// ------------------------------------------------------------------------
	public function getURL($relative=true)
	{
		return URL::to('projects/'.$this->id);
	}
}
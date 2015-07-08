<?php namespace Asset;

use Str;
use File;
use URL;
use User;

class Version extends \Eloquent {
  
    protected $fillable = [];
  
    // ------------------------------------------------------------------------
    public function __construct($attributes = array(), $exists = false) {
    	parent::__construct($attributes, $exists);
    }

    // ------------------------------------------------------------------------
    public function toArray() 
    {
    	$array = parent::toArray();
        return $array;
    }

    // ------------------------------------------------------------------------
    public function saveVersion($f, &$asset)
    {
    
        if(empty($this->filename)) {
            return 'Missing Filename';
        }
        $save_path = $asset->getRelativePath().'/versions';

        if(!File::exists($save_path)) {
            File::makeDirectory($save_path, 0755, true);     
        }

        $asset->currentVersion()->associate($this);
        $asset->save();

        $f->move($save_path, $this->filename);
    }

    // ------------------------------------------------------------------------
    public function getPath()
    {
        return $this->asset==NULL ? NULL : $this->asset->getRelativePath().'/versions/'.$this->filename;
    }

    // ------------------------------------------------------------------------
    public function user() {
      return $this->belongsTo('User');
    }

    // ------------------------------------------------------------------------
    public function asset() {
      return $this->belongsTo('Asset');
    }

    // ------------------------------------------------------------------------
    public function delete() {
      
      if( File::exists($this->getPath()) ) {
        File::deleteDirectory($this->getPath());
      }
      parent::delete();
    }

}
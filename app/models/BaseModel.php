<?php 

/**
* BaseModel 
*/
class BaseModel extends \Eloquent {
	
	public function getURL($relative=true) { }
	public function fireActivityEvent() { }
	public function isTrashed() { 
		return isset($this->deleted_at);
	}

  public static function getTableName()
  {
    return with(new static)->getTable();
  }

  public static function getRandomID()
  {
    $table = with(new static)->getTable();
    return DB::table($table)->orderByRaw("RAND()")->take(1)->lists('id')[0];
    // return $tablewith(new static)->getTable();
  }  

  public function getSafeSlug($name) {
      $name = empty($name) ? uniqid() : $name;
      $table = $this->getTable();
      $slug_to_save = Str::slug($name);
      $query = DB::table($table)->where('slug', 'LIKE', "%{$slug_to_save}%")->get();
      $slugsSearch = DB::table($table)
                    ->whereRaw("slug REGEXP '^{$slug_to_save}(-[0-9]*)?$'")
                    ->where('id','<>', $this->id) 
                    ->lists('slug');
     
      if(count($slugsSearch)>0) {
        $numbers = [];
        foreach ($slugsSearch as $item) {
          $end = strripos($item, '-');
          $val = substr($item, $end+1);
          if($end !==false && $val !==false && is_numeric($val)) {
            array_push($numbers, $val);
          }
        }
        rsort($numbers);
        $inc = reset($numbers) + 1;
        $slug_to_save = "{$slug_to_save}-{$inc}";
      }
      return $slug_to_save;
  }

  public static function findSafeSlug($name, $table, $id) {
      $name = empty($name) ? uniqid() : $name;
      $slug_to_save = Str::slug($name);
      $query = DB::table($table)->where('slug', 'LIKE', "%{$slug_to_save}%")->get();
      
      $slugsSearch = DB::table($table)
                    ->whereRaw("slug REGEXP '^{$slug_to_save}(-[0-9]*)?$'")
                    ->where('id','<>', $id) 
                    ->lists('slug');

      if(count($slugsSearch)>0) {
        $numbers = [];
        foreach ($slugsSearch as $item) {
          $end = strripos($item, '-');
          $val = substr($item, $end+1);
          if($end !==false && $val !==false && is_numeric($val)) {
            array_push($numbers, $val);
          }
        }
        rsort($numbers);
        $inc = reset($numbers) + 1;
        $slug_to_save = "{$slug_to_save}-{$inc}";
      }
      return $slug_to_save;
  }
}
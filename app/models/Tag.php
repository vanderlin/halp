<?php

class Tag extends \Eloquent {
	protected $fillable = [];

    // ------------------------------------------------------------------------
    public function toArray() {
        $array = parent::toArray();
        $array['url'] = URL::to('/spots/tags/'.$this->slug);
        return $array;
    }

    // ------------------------------------------------------------------------
    public static function findFromData($data) {
        return Tag::where('id', '=', $data)->orWhere('slug', '=', $data)->orWhere('name', '=', $data)->first();
    }

    // ------------------------------------------------------------------------
    public static function getTop($max=5) {
        $tags = DB::table('taggables')->groupBy('tag_id')->orderBy('count', 'DESC')->get(array('tag_id', DB::raw('count(*) as count')));
        
        $tags = array_slice($tags, 0, $max);
        $objs = [];
        foreach ($tags as $tag) {
          $t = Tag::find($tag->tag_id);
          $t->count = $tag->count;
          array_push($objs, $t);
        }
      
        return $collection = new Illuminate\Support\Collection($objs);

    }

    // ------------------------------------------------------------------------
    public function user() {
    	return $this->belongsTo('User');
  	}

  	// ------------------------------------------------------------------------
  	public function spots() {
     		return $this->morphedByMany('Spot', 'taggable');
  	}

	 // ------------------------------------------------------------------------
    public function getEditURL() {
      return '/admin/tags/'.$this->id.'/edit';
    }

    // ------------------------------------------------------------------------
    public function getURL($relative=true) {
      return $relative? '/spots/tags/'.$this->slug : URL::to('/tags/'.$this->slug);
    }

    // ------------------------------------------------------------------------
    public function delete() {
      $this->spots()->sync([]);
      parent::delete();
    }

}
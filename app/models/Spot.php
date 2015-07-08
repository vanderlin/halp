<?php

class Spot extends BaseModel {

    use \Assetable;
    use ActivityModelTrait;
    use SoftDeletingTrait;

	  protected $fillable = [];
    protected $with     = array('location');
    protected $dates    = ['deleted_at'];
    
    public static $draftRules = array('name'=> 'required',
                                      'user_id'=>'required|exists:users,id',
                                      'location_name'=> 'required',
                                      'location_lat'=> 'required',
                                      'location_lng'=> 'required',
                                      'place_id'=>'unique:locations,place_id'
                                    );

    public static $rules = array('name'=> 'required',
                                 'user_id'=>'required|exists:users,id',
                                 'location_name'=> 'required',
                                 'its_a'=>'required',
                                 'description'=>'required',
                                 'location_lat'=> 'required',
                                 'location_lng'=> 'required',
                                 'place_id'=>'unique:locations,place_id',
                                 'category'=>'required',
                                 // 'files'=>'required', // not for now...
                                 );
    
    //   public function save(array $options = array())
    // {
    //   dd($this);
    //    parent::save($options);
    // }    

    // ------------------------------------------------------------------------
    public function fireActivityCreatedEvent() {
        $activity = [
          'name' =>  Activity::ACTIVITY_SPOT_CREATED,
          'timestamp' => $this->updated_at,
          'parent' => $this,
          'user_id' => $this->user->id,
        ]; 
        $activity = (object)$activity;
        Event::fire($activity->name, array($activity)); 
    }

    // ------------------------------------------------------------------------
    public function fireActivityUpdatedEvent() {
        $activity = [
          'name' => Activity::ACTIVITY_SPOT_UPDATED,
          'timestamp' => $this->updated_at,
          'parent' => $this,
          'user_id' => $this->user->id,
        ]; 
        $activity = (object)$activity;
        Event::fire($activity->name, array($activity)); 
    }

     // ------------------------------------------------------------------------
    public function fireActivityDeleteEvent() {
        Event::fire(Activity::ACTIVITY_SPOT_DELETED, array($this)); 
    }

    // ------------------------------------------------------------------------
    public static function missingSpotImage() {
      $m = Asset::findFromTag('missing-spot');
      if($m == NULL) {
        return Asset::missingFile();   
      }
      return $m;
    }

    // ------------------------------------------------------------------------
    public function toArray() {
      $array = parent::toArray();
      
      if($this->location) {
        $array['lat'] = $this->location->lat;
        $array['lng'] = $this->location->lng;
        $array['latlng'] = $this->location?$this->location->latlngString():'';
      }
      
      $array['url'] = $this->getURL();
      $array['edit_url'] = $this->getEditURL();
      $array['thumbnail'] = URL::to($this->getThumbnail()->url());
      $array['thumbnail_base'] = URL::to($this->getThumbnail()->resizeImageURL());
      return $array;
    }

    // ------------------------------------------------------------------------
    public static function findInLocation($lat, $lng, $max_distance=25, $units='miles', $paginate=true) {

      switch ( $units ) {
            case 'miles':
                //radius of the great circle in miles
                $gr_circle_radius = 3959;
            break;
            case 'kilometers':
                //radius of the great circle in kilometers
                $gr_circle_radius = 6371;
            break;
      }

      $haversine = '('.$gr_circle_radius.' * acos(cos(radians(' . $lat . ')) * cos(radians(lat)) * cos(radians(lng) - radians(' . $lng . ')) + sin(radians(' . $lat . ')) * sin(radians(lat))))';;
      $radius = 1;
      

      
        
    
      $locations = DB::table('locations')->select( array('*', DB::raw($haversine . ' as distance')) )
                                         ->orderBy('distance', 'ASC')
                                         ->having('distance', '<=', $max_distance)
                                         ->having('locationable_type', '=', 'Spot')
                                         ->get();               

      $spots=[];
      foreach ($locations as $loc) {
        $location = Location::find($loc->id);
        if($location->locationable && $location->locationable->status=='Publish') {
          array_push($spots, $location->locationable);
        }
      }
      $collection = new Illuminate\Support\Collection($spots);
      
      $collection->sort(function($a, $b) {
        return $a->created_at->lt($b->created_at);
      });

      if($paginate) {
        $page = 1;
        if( Input::has('page') ) {
          $page = Input::get('page');
        }

        $perPage = 200;
        $offset = (($page - 1) * $perPage);

        return Paginator::make($collection->slice($offset, $perPage, true)->all(), $collection->count(), $perPage);
      }
      return $collection;
    }

    // ------------------------------------------------------------------------
    public function comments() {
      return $this->morphMany('Comment', 'commentable')->orderBy('created_at', 'DESC');
    }

    // ------------------------------------------------------------------------
    public function tags() {
      return $this->morphToMany('Tag', 'taggable');
    }
    public function getSelect2Tags() {
      $tags = [];
      foreach ($this->tags as $tag) {
        array_push($tags, ['id'=>$tag->id, 'text'=>$tag->name]);
      }
      return json_encode($tags);
    }
    public function tagIds() 
    {
      return $this->tags->lists('id');
    }

    // ------------------------------------------------------------------------
    public function visits() {
      return $this->hasMany('Visit');
    }

    // ------------------------------------------------------------------------
    public function hasVisits() {
      return $this->visits->count() > 0;
    }

    // ------------------------------------------------------------------------
    public function hasComments() {
      return $this->comments->count() > 0;
    }
   
    // ------------------------------------------------------------------------
    public function getTotalVisitsAttribute() {
      return $this->visits->count() + 1;
    }
   
    // ------------------------------------------------------------------------
    public function getCommentsFromUser($user) {
      $id = $user;
      if (is_object($user)) {
        $id = $user->id;
      }
      $comments = Comment::whereUserId($id)
                            ->whereCommentableType('Spot')
                            ->whereCommentableId($this->id)
                            ->get();                                       
      return $comments; 
    }

    // ------------------------------------------------------------------------
    public function getStatusAttribute($val) {
      if($val==null) {
        $this->status = 'Publish';
        $this->save();
      }
      return $val;
    } 

    // ------------------------------------------------------------------------
    public function getWebsiteAttribute($val) {
      if($this->location->details && array_key_exists('website', $this->location->details)) {
        return $this->location->details->website;
      }
      return "No website";
    } 

    // ------------------------------------------------------------------------
    public function getWebsiteDisplayName()
    {
      $url = $this->website;
      $host = parse_url($url, PHP_URL_HOST);
      $host = preg_replace('/^(www\.)/i', '', $host);
      return $host;
    }

    // ------------------------------------------------------------------------
    public function getPhoneNumberAttribute($val) {
      if($this->location->details && array_key_exists('formatted_phone_number', $this->location->details)) {
        return $this->location->details->formatted_phone_number;
      }
      return "No Phone Number";
    } 

    // ------------------------------------------------------------------------
    public function userDidFavorite($user) {
      return ($user && in_array($this->id, $user->favorites->getSpotIds() )) ? true : false;
    }

    // ------------------------------------------------------------------------
    public function userDidComment($user) {
      $comments = $this->getCommentsFromUser($user);
      return count($comments) > 0;
    }

    // ------------------------------------------------------------------------
    public function userDidVisit($user) {
      // if($user && $user->id==$this->user_id) return true;
      $did = ($user && Visit::whereUserId($user->id)->whereSpotId($this->id)->first()==null) ? false : true;
      return $did;
    }

    // ------------------------------------------------------------------------
    public function visitFromUser($user) {
      if(!$user) return null;
      return Visit::whereUserId($user->id)->whereSpotId($this->id)->first();
    }

    // ------------------------------------------------------------------------
    public static function getAll() {
      return Spot::whereStatus('Publish')->orderBy('created_at', 'DESC')->get();
    }

    // ------------------------------------------------------------------------
    public static function getRecent($perpage=12) {
      return Spot::whereStatus('Publish')->where('user_id', '<>', '')->orderBy('created_at', 'DESC')->paginate($perpage);
    }

    // ------------------------------------------------------------------------
    public function assets() {
      return $this->morphToMany('Asset', 'assetable')->withTimestamps();
    }

    // ------------------------------------------------------------------------
    public static function addURL() {
      return '/admin/spots/create';
    }

	  // ------------------------------------------------------------------------
	  public function location() {
      return $this->belongsTo('Location');
  	}

  	// ------------------------------------------------------------------------
  	public function user() {
    	return $this->belongsTo('User');
  	}

    // ------------------------------------------------------------------------
    public function isMine($orAdmin=false) {
      if(!Auth::check()) return false;
      return $this->user->id == Auth::user()->id;
    }

    // ------------------------------------------------------------------------
    public function canEdit()
    {
      return $this->isMine() || Auth::user()->can('manage_spots') || Auth::user()->hasRole('Admin');   
    }

    // ------------------------------------------------------------------------
    public function spotable() {
      return $this->morphTo();
    }

    // ------------------------------------------------------------------------
  	public function categories() {
    	return $this->morphToMany('Category', 'categorizable');
  	}

    // ------------------------------------------------------------------------
    public function photos() {
      return $this->morphToMany('Asset', 'assetable')->orderBy('order');
    }

    // ------------------------------------------------------------------------
    public function getEditURL() {
      return '/admin/spots/'.$this->id.'/edit';
    }

    // ------------------------------------------------------------------------
    public function getPostURL() {
      return '/admin/spots/'.$this->id;
    }

    // ------------------------------------------------------------------------
    /*public function getSafeSlug($name) {
      $name = empty($name) ? uniqid() : $name;
      $slug_to_save = Str::slug($name);
      $query = DB::table('spots')->where('slug', 'LIKE', "%{$slug_to_save}%")->get();
      $slugsSearch = DB::table('spots')->whereRaw("slug REGEXP '^{$slug_to_save}(-[0-9]*)?$'")->lists('slug');
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
    }*/

    // ------------------------------------------------------------------------
    public function getSlugAttribute($val) {
      
      $slug = $val;
      
      if($val == NULL) {
        $slug = $this->getSafeSlug($this->name);
        $this->slug = $slug;
        $this->timestamps = false;
        $this->save();
      }
      return $slug;
    }

    // ------------------------------------------------------------------------
    /*public function setNameAttribute($val) {
      $this->attributes['name'] = $val;
    }*/

    // ------------------------------------------------------------------------
    public function getURL($relative=true) {
      return $relative?'/spots/'.$this->slug:URL::to('/spots/'.$this->slug);
    }

    // ------------------------------------------------------------------------
    public function updateCategories($categories) {
      
      $cats_to_sync = [];
      if($categories) {
        foreach ($categories as $category_id) {
          $category = Category::find($category_id);
          if($category) {
            array_push($cats_to_sync, $category->id);
          }
        }
      }
      
      $this->categories()->sync($cats_to_sync, true);     
      $this->save();
      
    }

    // ------------------------------------------------------------------------
    public function getThumbnailImage() {
      $photos = $this->photos;
      if(count($photos)==0) {
        return Spot::missingSpotImage();
      }
      else {
        return $photos[0];
      }
    }

    // ------------------------------------------------------------------------
    public function getThumbnail() {
      return $this->getThumbnailImage();
    }

    // ------------------------------------------------------------------------
    public function hasThumbnail() {
      return $this->photos()->count() > 0;
    }

    // ------------------------------------------------------------------------
    public function hasCategory($category) {

      foreach ($this->categories as $c) {
        if(is_object($category)) {
          if($c->id == $category->id) return true;
        }
        else {
          if($c->id == $category) return true;
        }
      }
      return false;
    }

    // ------------------------------------------------------------------------
    public function hasLocation() {
      return $this->location()->count()>0;
    }

}
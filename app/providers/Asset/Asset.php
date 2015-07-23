<?php namespace Asset;

use Str;
use File;
use URL;
use User;

class Asset extends \Eloquent {
  
    use \SoftDeletingTrait;   
    
    protected $fillable = [];
    protected $missing_filename = 'missing.svg';
    protected $dates = ['deleted_at'];
    public $fromSeed = false;     
    const ASSET_RIGHTS_USER_OWENED      = 1;//"assets.rights.user.owned";
    const ASSET_RIGHTS_NOT_USER_OWENED  = 2;//"assets.rights.not.user.owned";
    const ASSET_RIGHTS_UNKNOWN          = 3;//"assets.rights.unknown";

    // ------------------------------------------------------------------------
    const ASSET_TYPE_IMAGE          = 'assets.type.image';
    const ASSET_TYPE_AUDIO          = 'assets.type.audio';
    const ASSET_TYPE_UNKNOWN        = 'assets.type.unknown';

    // ------------------------------------------------------------------------
    public function __construct($attributes = array(), $exists = false) {
      parent::__construct($attributes, $exists);
      $this->uid = uniqid();
    }

    // ------------------------------------------------------------------------
    public function toArray() {
        $array = parent::toArray();
        // return $array;
        if($this->type == Asset::ASSET_TYPE_IMAGE) {
          $array['url'] = URL::to($this->url());
          $array['base_url'] = URL::to($this->resizeImageURL());
          $array['extension'] = $this->getExtension();
        }
        else if($this->type == Asset::ASSET_TYPE_AUDIO) {
          $array['url'] = URL::to($this->getRelativeURL());
        }

        $array['files'] = File::files($this->getRelativePath());

        return $array;
    }

    // ------------------------------------------------------------------------
    static public function missingFile() {
      $m = new Asset;
      $m->uid = 'missing';
      $m->id = 'missing';
      $m->tag = 'missing';
      $m->path = "assets/content/common";
      $m->filename = 'missing.svg';
      $m->shared = 1;
      return $m;
    }

    // ------------------------------------------------------------------------
    public static function rightsToString($num)
    {
      switch ($num) {
        case 1:
          return "assets.rights.user.owned";
          break;
        case 2:
          return "assets.rights.not.user.owned";
          break;
        case 3:
          return "assets.rights.unknown";
          break;
      }
    }
    
    // ------------------------------------------------------------------------
    public static function parseSize($size) {

        preg_match('/w(\d+)/', $size, $wMatch);
        preg_match('/h(\d+)/', $size, $hMatch);
        preg_match('/s(\d+)/', $size, $sMatch);
        preg_match('/@2x/', $size, $retinaMatch);
        preg_match('/raw/', $size, $rawMatch);
        preg_match('/svg/', $size, $svgMatch);

        
        $w = $h = $s = NULL;


        if($retinaMatch) {
          $retina = true;
        }

        if($svgMatch) {
          $svg = true;
        }

        if(count($wMatch)>=2) {
          $w = $wMatch[1];
        }
        if(count($hMatch)>=2) {
          $h = $hMatch[1];
        }
        if(count($sMatch)>=2) {
          $s = $sMatch[1];
        }

        if($rawMatch) {
          $raw = true;
        }

        $width = NULL;
        $height = NULL;

        
        if($s) {
          $width = $height = $s;
        }
        if($w) {
          $width = $w; 
        }
        if($h) {
          $height = $h; 
        }
        return (object)['width'=>$width, 'height'=>$height];
    }


    // ------------------------------------------------------------------------
    public static function scopeFromUser($query, $id) {
        
        if(is_object($id)) {
          $id = $id->id;
        }
        return $query->where('user_id', '=', $id)
                        // ->join('assetables', function($j) {
                          // $j->on('assetables.asset_id', '=', 'assets.id');
                        // })
                        // ->whereHas('spot')
                        ->whereNotIn('assets.assetable_type', ['Category', 'Location']);
    }

    // ------------------------------------------------------------------------
    private function getGps($exifCoord, $hemi) {
      $degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
      $minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
      $seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;
      $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
      return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
    }

    private function gps2Num($coordPart) {
      $parts = explode('/', $coordPart);
      if (count($parts) <= 0) {
        return 0;
      }
      if (count($parts) == 1) {
        return $parts[0];
      }

      return floatval($parts[0]) / floatval($parts[1]);
    }

    public function getGPSLocationFromImageMeta()
    {
      if($this->fileExists() == false) return NULL;
      $exif = exif_read_data($this->getRelativeURL(false));
      if(array_key_exists('GPSLongitude', $exif) && array_key_exists('GPSLongitudeRef', $exif) && array_key_exists('GPSLatitude', $exif) && array_key_exists('GPSLatitudeRef', $exif))
      {
        $lon = $this->getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);
        $lat = $this->getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
        return ['lat'=>$lat, 'lng'=>$lon];       
      }
      return NULL;
    }

    // ------------------------------------------------------------------------
    public function scopeImages($query)
    {
        return $query->where('type', '=', Asset::ASSET_TYPE_IMAGE);
    }
    
    // ------------------------------------------------------------------------
    public static function scopeWithNoRights($query) {
        return $query->where('rights', '=', Asset::ASSET_RIGHTS_UNKNOWN)->orWhereNull('rights');
    }

    // ------------------------------------------------------------------------
    public function getMetaData()
    {
      return $this->fileExists() ? exif_read_data($this->getRelativeURL(false)) : NULL;
    }
    // ------------------------------------------------------------------------
    public function isMissingImage() {
      return $this->shared == 1;
    }

    // ------------------------------------------------------------------------
    public function getMissingImage() {
      return 'assets/content/common/missing.svg';
    }
    
    // ------------------------------------------------------------------------
    public function isOwnedByUser($user)
    {
      return $this->rights == Asset::ASSET_RIGHTS_USER_OWENED && ($user && $user->id == $this->user_id);
    }

    // ------------------------------------------------------------------------
    public static function findFromTag($tag) {
      return Asset::whereTag($tag)->first();
    }

    // ------------------------------------------------------------------------
    public function save(array $options = array()) {
      if($this->uid == 'missing') return;
      parent::save($options);
    }

     // ------------------------------------------------------------------------
    public function fileExists() {
      return File::exists($this->getFolder()) && File::exists($this->relativeURL());
    }

    // ------------------------------------------------------------------------
    public function missingReleationship() {
      if($this->assetable_type != NULL && $this->assetable_id != NULL) {
        $type = $this->assetable_type;
        $obj = $type::find($this->assetable_id);
        if($obj == NULL) {
          return true; 
        }
      }
      else {
        $query = DB::table('assetables')->where('asset_id', $this->id)->get();

        foreach ($query as $row) {
          if($row->assetable_type != NULL && $row->assetable_id != NULL) {
            $type = $row->assetable_type;
            $obj = $type::find($row->assetable_id);
             if($obj == NULL) {
              return true; 
             }
          }
        }
      }
      return false;
    }

    // ------------------------------------------------------------------------
    public function getMissingReleationship() {
      $missing = [];
      if($this->assetable_type != NULL && $this->assetable_id != NULL) {
        $type = $this->assetable_type;
        $obj = $type::find($this->assetable_id);
        if($obj == NULL) {
          array_push($missing, ['one-to-one', $this->assetable_type, $this->assetable_id]);
        }
      }
      else {
        $query = DB::table('assetables')->where('asset_id', $this->id)->get();

        foreach ($query as $row) {
          if($row->assetable_type != NULL && $row->assetable_id != NULL) {
            $type = $row->assetable_type;
            $obj = $type::find($row->assetable_id);
             if($obj == NULL) {
              array_push($missing, ['polymorphic', $row->assetable_type, $row->assetable_id]); 
             }
          }
        }
      }
      return $missing;
    }

    // ------------------------------------------------------------------------
    public function delete() {
      if($this->id == 'missing') return;
      if( File::exists($this->getFolder()) ) {
        File::deleteDirectory($this->getFolder());
      }
      parent::delete();
    }

    // ------------------------------------------------------------------------
    public function clearCache() {
      if($this->id == 'missing') return;
      $files = File::files($this->getRelativePath());
      foreach ($files as $f) {
        if($this->filename!==basename($f))
        {
          File::delete($f);
        }
      }
    }

    public function getPathAttribute($value)
    {
      if(isset($this->fromSeed)&&$this->fromSeed) {
        $value = public_path($value);
      }
      return $value;
    }
    // ------------------------------------------------------------------------
    public function getBasePath() 
    {
      return $this->path;
    }

    // ------------------------------------------------------------------------
    public function getFolder() 
    {
      return $this->path.'/'.$this->uid;
    }

    // ------------------------------------------------------------------------
    public function getClassName() 
    {
      $t = get_class($this);
      return strrchr($t, '\\') === false ? $t : substr(strrchr($t, '\\'), 1);
    }

    // ------------------------------------------------------------------------
    public function isImage()
    {
      if(isset($this->type)) {
        return $this->type == Asset::ASSET_TYPE_IMAGE;
      }
      
      $f = $this->relativeURL(true);
      $t = exif_imagetype($f);
      $types = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG];
      return in_array($t, $types);
    }

    // ------------------------------------------------------------------------
    public function isAudio()
    {

      if(isset($this->type)) {
        return $this->type == Asset::ASSET_TYPE_AUDIO;
      }
      $t = strtolower(File::extension($this->relativeURL()));
      $types = ['m4a'];
      return in_array($t, $types);
    }

    // ------------------------------------------------------------------------
    public function isSVG()
    {
      $url = $this->relativeURL();
      return File::extension($url) == 'svg';
    }

    // ------------------------------------------------------------------------
    public static function isAudioFile($f)
    {
      $types = ['m4a'];
      if(get_class($f) == 'Symfony\Component\HttpFoundation\File\UploadedFile') 
      {
        $t = $f->getClientOriginalExtension();
        return in_array($t, $types);
      }
      else if(property_exists($f, 'filePath')) {
        $t = strtolower(File::extension($f->filePath));
        return in_array($t, $types);
      }
      return false;
    }

    // ------------------------------------------------------------------------
    public static function isImageFile($f)
    { 
      if(get_class($f) == 'Symfony\Component\HttpFoundation\File\UploadedFile') 
      {
        $t = exif_imagetype($f);
        $types = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG];
        return in_array($t, $types);
      }
      else if(property_exists($f, 'filePath'))
      {
        $t = strtolower(File::extension($f->filePath));
        $types = ['jpg', 'jpeg', 'png'];
        return in_array($t, $types);
      }
      return false;
    }

    // ------------------------------------------------------------------------
    public function versions()
    {
      return $this->hasMany('Asset\Version');
    }

    // ------------------------------------------------------------------------
    public function hasVersions()
    {
      return $this->versions()->count() > 0;
    }

    // ------------------------------------------------------------------------
    public function currentVersion()
    {
      return $this->belongsTo('Asset\Version', 'version_id');
    }
    
    // ------------------------------------------------------------------------
    public function isActiveVersion($version)
    {
      return $this->currentVersion!=NULL && $this->currentVersion->id === $version->id;
    }

    // ------------------------------------------------------------------------
    public function user() {
      return $this->belongsTo('User');
    }

    // ------------------------------------------------------------------------
    public function generateUID($save=false) {
      $this->uid = uniqid();
      if($save) $this->save();
    }

    // ------------------------------------------------------------------------
    public function hasFile() {
      return $this->filename;
    }

    // ------------------------------------------------------------------------
    public function editURL() {
      return URL::to($this->table.'/'.$this->id.'/edit');
    }

    // ------------------------------------------------------------------------
    public function deleteURL() {
      return URL::to($this->table.'/'.$this->id); 
    }

    // ------------------------------------------------------------------------
    public function assetable() {
        return $this->morphTo()->withTimestamps();
    }

    // ------------------------------------------------------------------------
    public function getName() {
      if($this->name == null) return $this->filename;
      return $this->name;
    }

    // ------------------------------------------------------------------------
    public function isShared() {
      return ($this->shared === '1' || $this->shared === 1) ? true : false;
    }

    // ------------------------------------------------------------------------
    public function getSaveFilename($filename) {
      $info = pathinfo($filename);
      $name = Str::slug($info['filename']);
      $name = empty($name) ? uniqid() : $name;
      return $name.'.'.strtolower($info['extension']);
    }
    // ------------------------------------------------------------------------
    public function saveRemoteAsset($url, $filename, $type=Asset::ASSET_TYPE_UNKNOWN) {
      if($this->id == 'missing') return;
        
        $filename = $this->getSaveFilename($filename);
        $this->filename = $filename;
        $this->source   = $url;
        $this->org_filename = $url;
        $save_path = $this->getRelativePath();
        $this->type = $type;

        if(!File::exists($save_path)) {
          File::makeDirectory($save_path, 0755, true, true);     
        }
        $content = @get_remote_file($url);
        if($content) {
          file_put_contents($save_path.'/'.$filename, $content);
          $this->save();  
          return true;
        }
        return false;        
    }

    // ------------------------------------------------------------------------
    public function replace($file)
    {
      
      $this->removeOldFile();

      if(is_object($file)) {
        $this->saveFile($file);
      }
      else {
        $this->saveRemoteAsset($file, $this->filename);
      }

      return $this;
    }

    // ------------------------------------------------------------------------
    public function removeOldFile() {
      if(File::exists($this->getRelativePath())) {
        File::deleteDirectory($this->getRelativePath());
      }
    }

    // ------------------------------------------------------------------------
    public function saveEmailFile($f)
    {
        $this->removeOldFile();
        $save_path = $this->getRelativePath();
        $this->filename     = $this->getSaveFilename($f->name);
        $this->org_filename = $f->name;
        $this->source = $f->name;

        if(Asset::isImageFile($f)) {
          $this->type = Asset::ASSET_TYPE_IMAGE;
        }
        else if(Asset::isAudioFile($f)) {
          $this->type = Asset::ASSET_TYPE_AUDIO;
        }
        else {
          $this->type = Asset::ASSET_TYPE_UNKNOWN; 
        }

        if(!File::exists($save_path)) {
          File::makeDirectory($save_path, 0755, true);     
        }

        File::move($f->filePath, $save_path.'/'.$this->filename); 
        $this->save();
    }

    // ------------------------------------------------------------------------
    public function saveFile($f) 
    {
      
      if($this->uid == 'missing') return;
      
      $this->removeOldFile();
      $save_path = $this->getRelativePath();
      $this->filename     = $this->getSaveFilename($f->getClientOriginalName());
      $this->org_filename = $f->getClientOriginalName();
      $this->source = $f->getClientOriginalName();

      if(Asset::isImageFile($f)) {
        $this->type = Asset::ASSET_TYPE_IMAGE;
      }
      else if(Asset::isAudioFile($f)) {
        $this->type = Asset::ASSET_TYPE_AUDIO;
      }
      else {
        $this->type = Asset::ASSET_TYPE_UNKNOWN; 
      }

      if(!File::exists($save_path)) {
        File::makeDirectory($save_path, 0755, true);     
      }

      $f->move($save_path, $this->filename);
      $this->save();
    }

    // ------------------------------------------------------------------------
    public function saveFileData($data, $filename) {
      if($this->uid == 'missing') return;
      $uri =  substr($data,strpos($data,",")+1);
      $this->removeOldFile();

      $save_path = $this->getRelativePath();
      
      if(!File::exists($save_path)) {
        File::makeDirectory($save_path, 0755, true);     
      }
      $filename = $this->getSaveFilename($filename);
      $this->filename = $filename;
      $this->org_filename = $filename;
      $this->source = $filename;
      file_put_contents($this->getRelativePath().'/'.$filename, base64_decode($uri));
      $this->save();
    }
    // ------------------------------------------------------------------------
    /*public function saveImageData($data, $save_path, $filename) {
      if($this->id == 'missing') return;
      $uri =  substr($data,strpos($data,",")+1);
      $this->filename = $filename;
      if(!File::exists($save_path)) {
        File::makeDirectory($save_path, 0755, true);      
      }
      file_put_contents($save_path.'/'.$filename, base64_decode($uri));
    }*/

    // ------------------------------------------------------------------------
    public function resizeImageURL($options=array()) {
      return URL::to('images/'.$this->id.(is_string($options)?'/'.$options:''));  
    }

    // ------------------------------------------------------------------------
    public function getUrlAttribute() {
      return URL::to($this->path.'/'.$this->uid.'/'.$this->filename);
    }

    // ------------------------------------------------------------------------
    public function getExtension() {
      return pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    // ------------------------------------------------------------------------
    public function resize($size) {

      $dotpos = stripos($size, '.');
      if($dotpos) {
        $size = substr($size, 0, $dotpos);
      }

      $url    = $this->relativeURL();
      $w      = null;
      $h      = null;
      $s      = null;
      $raw  = false;
      $svg  = false;
      $retina = false;

      if(File::exists($url) == false || empty($this->filename)) {
        $file = AssetsController::missingImageResponse();
        return (object)['file'=>$file, 'mime'=>'image/svg+xml', 'width'=>$w, 'height'=>$h, 'attr'=>'width="'.$w.'" height="'.$h.'"'];
      }

      if($size != null) {

        preg_match('/w(\d+)/', $size, $wMatch);
        preg_match('/h(\d+)/', $size, $hMatch);
        preg_match('/s(\d+)/', $size, $sMatch);
        preg_match('/@2x/', $size, $retinaMatch);
        preg_match('/raw/', $size, $rawMatch);
        preg_match('/svg/', $size, $svgMatch);

        if($retinaMatch) {
          $retina = true;
        }

        if($svgMatch) {
          $svg = true;
        }

        if(count($wMatch)>=2) {
          $w = $wMatch[1];
        }
        if(count($hMatch)>=2) {
          $h = $hMatch[1];
        }
        if(count($sMatch)>=2) {
          $s = $sMatch[1];
        }

        if($rawMatch) {
          $raw = true;
        }
        
      }


      // if we are svg just return the file
      // todo: parse svg and alter width/height
      if(File::extension($url) == 'svg') {
        
        $svg = File::get($url);
        
        if($s!=null) {
          if($retina) $s *= 2;
          $svg = AssetsController::resizeSVG($svg, $s, null);
        }
        else if($w!=null||$h!=null) {
          if($retina) {
            if($w!=null)$w *= 2;
            if($h!=null)$h *= 2;

          }
          $svg = AssetsController::resizeSVG($svg, $w, $h);
        } 
        
        if($raw) {
          $str = strpos($svg, "<svg") ? substr($svg, strpos($svg, "<svg")) : $svg;
          return (object)['file'=>$str, 'mime'=>'image/svg+xml', 'width'=>$w, 'height'=>$h, 'attr'=>'width="'.$w.'" height="'.$h.'"'];
          // return Response::make($str, 200, array('Content-Type' => 'image/svg+xml'));
        }
        return (object)['file'=>$svg, 'mime'=>'image/svg+xml', 'width'=>$w, 'height'=>$h, 'attr'=>'width="'.$w.'" height="'.$h.'"'];
        // return Response::make($svg, 200, array('Content-Type' => 'image/svg+xml'));
      }



      $info = pathinfo($url);
      $filename = $size ? ($info['filename'].'_'.$size.'.'.$info['extension']) : $info['filename'].'.'.$info['extension'];
      $path = $info['dirname'].'/'.$filename;
      

      if(File::exists($path)) {
        $file = new \Symfony\Component\HttpFoundation\File\File($path);
        $mime = $file->getMimeType();
        $image_size = getimagesize($path);

        return (object)['path'=>$path,
                        'file'=>File::get($path), 
                        'mime'=>$mime, 
                        'width'=>$image_size[0], 
                        'height'=>$image_size[1],
                        'style'=>'width:'.$image_size[0].'px; height:'.$image_size[1].'px;',
                        'attr'=>'width="'.$image_size[0].'" height="'.$image_size[1].'"'];
      }
      
      

      $img = Image::cache(function($image) use($url, $w, $h, $s, $path, $retina) {
        
        $image->make($url);

        

        if($s!=null) {
          if($retina) $s *= 2;
          
          $image->fit($s, $s)->sharpen(3);

          /*
          $image->resize($desw, $desh, function ($constraint) {
              $constraint->aspectRatio();
          })->crop($s, $s);*/
        }
        else if($w!=null||$h!=null) {
          if($retina) {
            if($w!=null)$w *= 2;
            if($h!=null)$h *= 2;

          }
          
          $image->resize($w, $h, function ($constraint) {
              $constraint->aspectRatio();
          });
        }
        $image->save($path);

        return $image;
      });

      $file = new \Symfony\Component\HttpFoundation\File\File($url);
      $mime = $file->getMimeType();

      return (object)['path'=>$path, 'file'=>$file, 'mime'=>$mime];
    }

    // ------------------------------------------------------------------------
    public function obj($options=array(), $relative=true) {
      $res = $this->resize($options);
      $res->url = $this->url($options, $relative);
      return $res;
    }

    // ------------------------------------------------------------------------
    public function url($options=array(), $relative=true) {

      $ext = $this->getExtension();

      if($relative) {
        return '/images/'.$this->id.(is_string($options)?'/'.$options:'').'.'.$ext;
      }
      return URL::to('images/'.$this->id.(is_string($options)?'/'.$options:'').'.'.$ext);  
      // return URL::to('images/'.strtolower($this->getClassName()).'/'.$this->id.(is_string($options)?'/'.$options:''));  
    }

    // ------------------------------------------------------------------------
    public function uri($options=array()) {
      

      $ext = $this->getExtension();
      $url = URL::to('images/'.$this->id.(is_string($options)?'/'.$options:'').'.'.$ext);
      $imageData = base64_encode(file_get_contents($url));

      // Format the image SRC:  data:{mime};base64,{data};
      return 'data:'.$this->getMimeType().';base64,'.$imageData;

    }

    // ------------------------------------------------------------------------
    public function getMimeType()
    {
      $file = new \Symfony\Component\HttpFoundation\File\File($this->relativeURL());
      return $file->getMimeType();
    }

    // ------------------------------------------------------------------------
    public function svg() {
        $url = $this->relativeURL();
        $svg = File::get($url);
        return $svg;
    }

    // ------------------------------------------------------------------------
    public function getRelativePath($options=array(), $relative=true) {
      return $this->path.'/'.$this->uid;
    }

    // ------------------------------------------------------------------------
    public function relativeURL($options=array(), $relative=true) {
      return $this->path.'/'.$this->uid.'/'.$this->filename;
    }

      // ------------------------------------------------------------------------
    public function getRelativeURL($relative=true) {
      if($this->isAudio()) {
        $version = $this->currentVersion;
        if($version!==NULL) {
          return ($relative?'/':'') . $this->path .'/'. $this->uid .'/'. 'versions/'.$version->filename;  
        }
        
      }
      return ($relative?'/':'').$this->path.'/'.$this->uid.'/'.$this->filename;
    }




}
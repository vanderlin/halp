<?php namespace Asset;

use Carbon\Carbon;
use DB;
use \Illuminate\Support\Collection as Collection;
use Input;
use Paginator;
use User;
use Str;
use Response;
use File;
use Image;
use Validator;
use Request;

/**
* Assets Repository
*/

class AssetsRepository  {
	
	private $listener;

	// ------------------------------------------------------------------------
	public function __construct() {
		
	}

	// ------------------------------------------------------------------------
	public function setListener($listener) {
		$this->listener = $listener;
	}

	// ------------------------------------------------------------------------
	public function missingImageResponse() 
	{
		$temp = Asset::missingFile();

		$size = Request::segment(3);
		return $this->resizeImage($temp, $size);

		$missingFile = new \Symfony\Component\HttpFoundation\File\File($temp->getMissingImage());
		$mimeType = $missingFile->getMimeType();
		if($missingFile->getMimeType() == 'text/plain') $mimeType = 'image/svg+xml';
		return Response::make(File::get($temp->getMissingImage()), 200, array('Content-Type' => $mimeType));
	}

	// ------------------------------------------------------------------------
    public static function parseSizeFromString($size) 
    {

		$w = null;
		$h = null;
		$s = null;
		$retina = null;
		$svg = null;
		$raw = null;
		$uri = null;

		if($size != null) {

			preg_match('/w(\d+)/', $size, $wMatch);
			preg_match('/h(\d+)/', $size, $hMatch);
			preg_match('/s(\d+)/', $size, $sMatch);
			preg_match('/@2x/', $size, $retinaMatch);
			preg_match('/raw/', $size, $rawMatch);
			preg_match('/svg/', $size, $svgMatch);
			preg_match('/uri/', $size, $uriMatch);


			if($retinaMatch) {
				$retina = true;
			}

			if($uriMatch) {
				$uri = true;
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
        return (object)['width'=>$w, 'height'=>$h, 'w'=>$w, 'h'=>$h, 'svg'=>$svg, 's'=>$s, 'uri'=>$uri, 'retina'=>$retina, 'raw'=>$raw];
    }

    // ------------------------------------------------------------------------
	public function getInputFiles() 
	{
		$files = [];
		if(Input::file('files')) {
			foreach (Input::file('files') as $f) {
				array_push($files, $f);
			}
		}
		if(Input::has('urls')) {
			foreach (Input::get('urls') as $f) {
				array_push($files, $f);
			}
		}
		
		if(Input::file('file') && !is_array(Input::file('file'))) {
			array_push($files, Input::file('file'));
		}
		if(Input::has('url') && !is_array(Input::file('url'))) {
			array_push($files, Input::file('url'));
		}
		return $files;
	}

    // ------------------------------------------------------------------------
	public function resizeSVG($svg, $w, $h) 
	{

		$reW = '/(.*<svg[^>]* width=")([\d.]+px)(.*)/si';
	    $reH = '/(.*<svg[^>]* height=")([\d.]+px)(.*)/si';
	    preg_match($reW, $svg, $mw);
	    preg_match($reH, $svg, $mh);
	    $width = floatval($mw[2]);
	    $height = floatval($mh[2]);

	    
	    if (!$width || !$height) return false;

	    $ratio = $height / $width;

	    $width = $w;
	    $height = $w * $ratio;

	    $svg = preg_replace($reW, "\${1}{$width}px\${3}", $svg);
	    $svg = preg_replace($reH, "\${1}{$height}px\${3}", $svg);
	    
	    return $svg;
	}

	// ------------------------------------------------------------------------
	public function resizeImage($id, $size=null) 
	{
		
		$asset = is_object($id) ? $id : $this->find($id);
		

		if($asset == null) return $this->missingImageResponse();		            



		if($asset->isImage() == false && $asset->isSVG() == false) {
			return 'Error: Wrong File Format';
		}

		
		$url    = $asset->relativeURL();
		$params = $this->parseSizeFromString($size);

		// if we are svg just return the file
		// todo: parse svg and alter width/height
		if($asset->isSVG()) {
			
			$svgFile = File::get($url);
			

			if($params->s!=null) {
				if($params->retina) $params->s *= 2;
				$svgFile = $this->resizeSVG($svgFile, $params->s, null);
			}
			else if($params->w!=null||$params->h!=null) {
				if($params->retina) {
					if($params->w!=null)$params->w *= 2;
					if($params->h!=null)$params->h *= 2;

				}
				$svgFile = $this->resizeSVG($svgFile, $params->w, $params->h);
			}	
			
			if($params->raw) {
				$str = strpos($svgFile, "<svg") ? substr($svgFile, strpos($svgFile, "<svg")) : $svgFile;
				return Response::make($str, 200, array('Content-Type' => 'image/svg+xml'));
			}
			return Response::make($svgFile, 200, array('Content-Type' => 'image/svg+xml'));
		}


		$info = pathinfo($url);
		
		$basename = str_until($size, ".");
		$filename = $size ? ($info['filename'].'_'.$basename.'.'.$info['extension']) : $info['filename'].'.'.$info['extension'];
		$path = $info['dirname'].'/'.$filename;

		if(File::exists($path)) {
			$file = new \Symfony\Component\HttpFoundation\File\File($path);
	        $mime = $file->getMimeType();
			return Response::make(File::get($path), 200, array('Content-Type' => $mime));
		}
		

		$img = Image::cache(function($image) use($url, $params, $path) {
			
			$image->make($url);
	
			if($params->s!=null) {
				if($params->retina) $params->s *= 2;
				
				$image->fit($params->s, $params->s)->sharpen(3);

				/*
				$image->resize($desw, $desh, function ($constraint) {
    				$constraint->aspectRatio();
				})->crop($s, $s);*/
			}
			else if($params->w!=null||$params->h!=null) {
				if($params->retina) {
					if($params->w!=null)$params->w *= 2;
					if($params->h!=null)$params->h *= 2;

				}
				
				$image->resize($params->w, $params->h, function ($constraint) {
    				$constraint->aspectRatio();
				});
			}

			$image->save($path);

			return $image;
		
		});

		$file = new \Symfony\Component\HttpFoundation\File\File($url);
        $mime = $file->getMimeType();

		return Response::make($img, 200, array('Content-Type' => $mime));
	}

	// ------------------------------------------------------------------------
	public function find($id) {
		if(is_object($id)) {
			$id = $id->id;
		}
		return Asset::withTrashed()->whereId($id)->first();
	}

	// ------------------------------------------------------------------------
	public function isImage($f)
	{
		$t = exif_imagetype($f);
		$types = [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG];
		return in_array($t, $types);
	}

	// ------------------------------------------------------------------------
	public function isAudio($f)
	{
		$t = strtolower($f->getClientOriginalExtension());
		$types = ['m4a'];
		return in_array($t, $types);
	}

	// ------------------------------------------------------------------------
	public function getPathForFile($f)
	{
		if($this->isImage($f)) {
			return 'assets/uploads/images';
		}
		else if($this->isAudio($f)) {
			return 'assets/uploads/audio';
		}
		return 'assets/uploads';
	}
	// ------------------------------------------------------------------------
	public function store($input)
	{		
		$files = $this->getInputFiles();
		if(empty($files)) {
			return $this->listener->errorResponse('No input files');
		}

		if (Input::has('type')&&Input::has('id')) {
				
			
			$validator = Validator::make($input, [  'type'	=>'required',
													'id'	=>'required',
													'path'	=>'required'
												]);
			
			$property = Input::get('property', 'assets');

			if($validator->fails()) {
				return $this->listener->errorResponse($validator->errors()->all());
			}

			$type = ucfirst(Input::get('type'));
			$id = Input::get('id');
			$item = $type::withTrashed()->whereId($id)->first();
			
			if($item == NULL) {
				return $this->statusResponse(['error'=>'No item found']);								
			}			
		}

		$validator = Validator::make($input, ['user_id' =>'required']);
		if($validator->fails()) {
			return $this->listener->errorResponse($validator->errors()->all());
		}
		
		$assets = [];
		foreach ($files as $f) {
			$asset = new Asset;
			$asset->path = Input::get('path', $this->getPathForFile($f));
			
			if(Input::has('tag')) {
				$asset->tag = Str::slug(strtolower(Input::get('tag')));
			}
			if(Input::has('shared')) {
				$asset->shared = Input::get('shared')=='on'?1:0;
			}


			$user  = User::findFromData($input['user_id']);
			$asset->user()->associate($user);
			
			$asset->saveFile($f);

			array_push($assets, $asset);
		}
		return $this->listener->statusResponse(['notice'=>'Asset Created', 'asset'=>$assets]);		


		$replace = false;
		if(array_key_exists('replace', $input) && bool_val($input['replace']) === true) {
			$replace = true;
		}

		$rights = Input::get('rights', Asset::ASSET_RIGHTS_UNKNOWN);
		$photos_response = $this->addPhotosToItem($this->getInputFiles(), $item, Input::get('path'), $replace, $rights);		
		
		return $this->listener->statusResponse(['notice'=>'Asset Created', 'asset'=>$asset]);		
	}

	// ------------------------------------------------------------------------
	public function delete() 
	{
		if(is_object($id)) {
			$id = $id->id;
		}
		$asset = Asset::withTrashed()->whereId($id)->first();
		if($asset) 
		{
			$asset->delete();
		}
		return $this->listener->statusResponse(['asset'=>$asset]);
	}
}
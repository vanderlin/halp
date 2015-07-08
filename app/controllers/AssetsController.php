<?php 

use Asset\Version;

class AssetsController extends \BaseController {

	private $repository;

	public function __construct(Asset\AssetsRepository $repository) 
	{
		$this->repository = $repository;
		$this->repository->setListener($this);
	}

	// ------------------------------------------------------------------------
	public function index() {
		return 'No Image Found';
	}

	// ------------------------------------------------------------------------
	public function store() {

		return $this->repository->store(Input::all());

		return;
		$input 	   = Input::all();
		$wantsJson = Input::wantsJson();
		$rules = array(
			'file'=> 'required',
		);

		$validate = Validator::make($input, $rules);
		
		
		// -----------------------------
		// error with the location / data
		// -----------------------------
		if($validate->fails()) {
			return $this->statusResponse(['errors'=>$validate->errors()->all(), 'input'=>$input]);	
		}
		
		$file  = Input::file('file');
		$asset = new Asset;	
		if(Input::has('shared')) {
			$asset->shared = Input::get('shared')=='on'?1:0;
		}

		if(Input::has('tag')) {
			$asset->tag = Str::slug(strtolower(Input::get('tag')));
		}

		$asset->path = "assets/content/uploads";
		
		$asset->saveFile($file);

		return $this->statusResponse(['asset'=>$asset]);	
	}

	// ------------------------------------------------------------------------
	public function storeVersion($id)
	{
		$asset = Asset::find($id);
		if($asset == null) return Redirect::back()->with(['error'=>'No asset found']);

		$file = Input::file('file');
		if($file) {
			
			$ext = $file->getClientOriginalExtension();

			$version = new Version;
			$version->save();

			$filename = $asset->uid.'_'.$version->id.'.'.$ext;
			$version->asset()->associate($asset);
			$version->user()->associate(Auth::user());
			$version->filename = $filename;
			$version->save();
			

			$version->saveVersion($file, $asset);			

			return $this->statusResponse(['asset'=>$asset, 'version'=>$version]);	

			// $asset->saveVersion($file, )

		}

		return $this->errorResponse('no file to save');
	}

	// ------------------------------------------------------------------------
	public function deleteVersion($asset_id, $version_id)
	{
		$asset = Asset::find($asset_id);
		if($asset == null) return Redirect::back()->with(['error'=>'No asset found']);

		$version = Version::find($version_id);
		if($version == null) return Redirect::back()->with(['error'=>'No version found']);

		$version->delete();

		return $this->statusResponse(['asset'=>$asset, 'version'=>$version]);	
	}
	
	// ------------------------------------------------------------------------
	public function update($id) {

		$asset = Asset::find($id);
		if($asset == null) return Redirect::back()->with(['error'=>'No asset found']);

		if(Input::has('name')) {
			$asset->name = Input::get('name');
		}
		if(Input::has('tag')) {
			$asset->tag = Str::slug(strtolower(Input::get('tag')));
		}
		if(Input::has('shared')) {
			$asset->shared = Input::get('shared')=='on'?1:0;
		}
		if(Input::has('rights')) {
			$asset->rights = Input::get('rights');
		}


		$file = Input::file('file');
		if($file) {
			$asset->saveFile($file);
		}

		$asset->touch();
		$asset->save();
		return $this->statusResponse(['notice'=>'Asset updated', 'asset'=>$asset]);
	}

	// ------------------------------------------------------------------------
	public static function missingImageResponse() {
		$temp = Asset::missingFile();
		
		$missingFile = new \Symfony\Component\HttpFoundation\File\File($temp->getMissingImage());
		$mimeType = $missingFile->getMimeType();
		if($missingFile->getMimeType() == 'text/plain') $mimeType = 'image/svg+xml';
		return Response::make(File::get($temp->getMissingImage()), 200, array('Content-Type' => $mimeType));
	}

	// ------------------------------------------------------------------------
	public static function resizeSVG($svg, $w, $h) {

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
	public function resizeImage($id, $size=null) {
		return $this->repository->resizeImage($id, $size);
	}

	// ------------------------------------------------------------------------
	public function show($id)
	{
		$asset = $this->repository->find($id);
		if($asset==null) return $this->errorResponse('no asset found');
		return $this->statusResponse(['asset'=>$asset]);
	}

	// ------------------------------------------------------------------------
	public function clearCache($id)
	{
		$asset = $this->repository->find($id);
		if($asset==null) return $this->errorResponse('no asset found');
		
		$asset->clearCache();

		return $this->statusResponse(['asset'=>$asset]);
	}

	// ------------------------------------------------------------------------
	public function clearAllCache()
	{
		$assets = Asset::all();
		foreach ($assets as $a) {
			$a->clearCache();
		}
		return $assets;
	}

	// ------------------------------------------------------------------------
	public function meta($id)
	{
		$asset = $this->repository->find($id);
		if($asset==null) return $this->errorResponse('no asset found');

		return $this->statusResponse(['asset'=>$asset, 'meta'=>$asset->getMetaData()]);
	}

	// ------------------------------------------------------------------------
	public function audio($id)
	{
		$asset = $this->repository->find($id);
		if($asset==null) return $this->errorResponse('no asset found');

		return $this->statusResponse(['asset'=>$asset]);
	}

	// ------------------------------------------------------------------------
	public function edit($id) {
		$asset = Asset::find($id);
		if($asset == null) return Redirect::back()->with(['error'=>'No asset found']);

		$file = Input::file('file');

		if($file) {
			
			

			$old_file = "{$asset->path}/{$asset->filename}";
			$destination = 'assets/uploads';
			$asset->filename = "{$asset->uid}.{$file->getClientOriginalExtension()}";
			$asset->org_filename = $file->getClientOriginalName();
			$asset->path = $destination;

			$asset->generateUID();
			$asset->filename = "{$asset->uid}.{$file->getClientOriginalExtension()}";

		
			File::delete($old_file);		
			$file->move($destination, $asset->filename);

		}
		
		if(Input::has('name')) {
			$asset->name = Input::get('name');
		}


		if(Input::has('id') && Input::has('type')) {
			$asset->assetable_id = Input::get('id');
			$asset->assetable_type = Input::get('type');
		}

		$asset->save();
		
		return Redirect::back()->with(['notice'=>'Asset updated']);
	}

	// ------------------------------------------------------------------------
	public function addPhotosToItem($files, &$item, $path, $replace=false, $rights=Asset::ASSET_RIGHTS_UNKNOWN) 
	{
		
		$property = Input::get('property', 'assets');
		
		$resp = [];
		$errors = [];
		if ($files) {
			foreach ($files as $file) {
				
				$photo = NULL;
				
				if($replace == true) {
					$photo = $item->$property()->first();
				}
				if($photo == NULL || $replace == false) {
					$photo = new Asset;	
					$photo->path = $path;
					$photo->rights = $rights;
					$photo->user()->associate(Auth::user());
				}

				
				if(is_object($file)) {
					$photo->saveFile($file);
				}
				else {
					$filename = uniqid().".jpg";
					if($photo->saveRemoteImage($file, $filename)) {
					}
					else {
						array_push($errors, "Could not save remote image - ".$file);
					}
				}
				array_push($resp, $photo);

				if($replace == false) {
					$item->$property()->attach($photo);	
				}
				else {
					$item->$property()->sync([$photo->id]);
				}
				
			}
		}
		return array('errors'=>$errors, 'files'=>$resp);
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
		return $files;
	}

	// ------------------------------------------------------------------------
	public function upload() {
		
		

		$wantsJson = Request::wantsJson();
		$input = Input::all();
		$validator = Validator::make($input, [  'type'	=>'required',
												'id'	=>'required',
												'path'	=>'required'
												]);
		
		$property = Input::get('property', 'assets');

		if($validator->fails()) {
			return $this->errorResponse($validator->errors()->all());
		}


		$type = ucfirst(Input::get('type'));
		$id = Input::get('id');
		$item = $type::withTrashed()->whereId($id)->first();
		

		if($item == NULL) {
			return $this->statusResponse(['error'=>'No item found']);								
		}

		$replace = false;
		if(array_key_exists('replace', $input) && bool_val($input['replace']) === true) {
			$replace = true;
		}

		$rights = Input::get('rights', Asset::ASSET_RIGHTS_UNKNOWN);
		$photos_response = $this->addPhotosToItem($this->getInputFiles(), $item, Input::get('path'), $replace, $rights);		
		return $this->statusResponse(['response'=>$photos_response]);					

	}

	// ------------------------------------------------------------------------
	public function delete($id) {

		$wantsJson = Request::wantsJson();
		$asset = Asset::find($id);
		if($asset == null) return $wantsJson ? Response::json(['errors'=>'No asset found', 'status'=>404, 'id'=>$id]) : Redirect::back()->with(['errors'=>'No asset found', 'status'=>404]);

		$total	 	= 0;
		$assetables = DB::table('assetables')->where('asset_id', $id)->get();
		
		if($assetables) {

			$item = null;
			foreach ($assetables as $as) {
				$as_type = $as->assetable_type;
				$as_id 	 = $as->assetable_id;
				$item 	 = $as_type::find($as_id);
			
				if($item) {
					$item->assets()->detach($asset);
					$total = $item->assets->count();
				}			
			}
		}

		$asset->delete();
		return $wantsJson ? Response::json(['notice'=>'Asset deleted', 'status'=>200, 'id'=>$id, 'total'=>$total]) : Redirect::back()->with(['error'=>'Asset deleted', 'status'=>200, 'id'=>$id, 'total'=>$total]);

	}

}
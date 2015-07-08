<?php

// --------------------------------------------------------------------------
// Admin / Roles
// --------------------------------------------------------------------------
 Route::group(['prefix'=>'admin', 'before'=>'auth'], function() {
	
	
	Route::get('/', function() {
		return View::make('admin.profile', ['user'=>Auth::user(), 'active_link'=>'index']);
	});

	Route::get('profile', function() {
		return View::make('admin.profile', ['user'=>Auth::user(), 'active_link'=>'index']);
	});

	Route::get('data', function() {
		if (Auth::user()->hasRole('Editor')) {
			
			$date = Input::get('date', date('2000-1-1')); // ---> launch day date('2015-1-23');
			
			$visits = Visit::where('created_at', '>=', $date)->get();
			$users = User::where('created_at', '>=', $date)->get();
			$spots = Spot::where('created_at', '>=', $date)->get();
			$itineraries = Itinerary::where('created_at', '>=', $date)->get();
			$with_data = Input::get('data', false);
			$data = [
				'itineraries' => ['total'=>count($itineraries), 'data'=>$with_data?$itineraries:null],
				'spots' => ['total'=>count($spots), 'data'=>$with_data?$spots:null],
				'users' => ['total'=>count($users), 'data'=>$with_data?$users:null],
				'visits' => ['total'=>count($visits), 'data'=>$with_data?$visits:null],
				'total_spots'=>Spot::all()->count(),
				'total_users'=>User::all()->count(),
				'total_itineraries'=>Itinerary::all()->count(),
				];
			return View::make('admin.data', ['data'=>$data]);
		}
	});
	
	// ------------------------------------------------------------------------
	// Blog
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'blog'], function() {
		Route::get('/', ['uses'=>'BlogController@adminIndex']);

		Route::get('post', ['uses'=>'BlogController@createBlogPost']);
		Route::post('post', ['uses'=>'BlogController@storeBlogPost', 'as'=>'admin.blog.store']);
		Route::put('post/{id}', ['uses'=>'BlogController@updateBlogPost', 'as'=>'admin.blog.update']);
		Route::delete('post/{id}', ['uses'=>'BlogController@deleteBlogPost', 'as'=>'admin.blog.delete']);
		Route::get('post/{id}', ['uses'=>'BlogController@editPost']);

		
		Route::get('types', ['uses'=>'BlogController@showPostTypes']);
		Route::post('types', ['uses'=>'BlogController@storePostType']);
		Route::put('types/{id}', ['uses'=>'BlogController@updatePostType']);
			
	});

	// ------------------------------------------------------------------------
	// Notifications
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'notifications'], function() {
		Route::get('/', function() {
			return View::make('admin.notifications.index', ['active_link'=>'notifications', 'notifications'=>Notification\Notification::siteNotifications()->get()]);
		});
		Route::post('/', ['uses'=>'NotificationsController@store', 'as'=>'admin.notice.store']);
	});

    // ------------------------------------------------------------------------
	// Debug CMS
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'debug'], function() {
		Route::get('/', function() {
			return View::make('admin.debug.index', ['active_link'=>'debug']);
		});
	});
	Route::get('locations', function() {
		$locations = Location::paginate(10);
		return View::make('admin.locations.index', ['active_link'=>'locations', 'locations'=>$locations]);
	});

	// ------------------------------------------------------------------------
	// Tags CMS
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'tags'], function() {
		Route::get('/', function() {
			return View::make('admin.tags.index', ['active_link'=>'tags']);
		});
		Route::get('{id}', function($id) {
			return View::make('admin.tags.edit', ['active_link'=>'tags', 'tag'=>Tag::find($id)]);
		});
		Route::post('/', ['uses'=>'TagsController@store']);
		Route::put('{id}', ['uses'=>'TagsController@update']);
		Route::delete('{id}', ['uses'=>'TagsController@destroy']);
	});


	// ------------------------------------------------------------------------
	// FAQs CMS
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'faqs'], function() {
		Route::get('/', function() {
			return View::make('admin.faq.index', ['active_link'=>'faq']);
		});
		Route::get('{id}', function($id) {
			return View::make('admin.faq.edit', ['active_link'=>'faq', 'faq'=>FAQ::find($id)]);
		});
		Route::post('/', ['uses'=>'FAQsController@store']);
		Route::put('{id}', ['uses'=>'FAQsController@update']);
		Route::delete('{id}', ['uses'=>'FAQsController@destroy']);
	});


	// ------------------------------------------------------------------------
	// Locations CMS
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'locations'], function() {

		Route::post('/', ['uses'=>'LocationsController@store', 'before'=>'auth']);
		Route::put('{id}/details', ['uses'=>'LocationsController@reloadDetails', 'before'=>'auth']);

	});

	// ------------------------------------------------------------------------
	// Spots CMS
	// ------------------------------------------------------------------------
	Route::group(['prefix'=>'spots', 'before'=>'auth'], function() {
		
		Route::put('{id}', 'SpotsController@update');
		Route::delete('{id}', 'SpotsController@destroy');
		Route::post('/', 'SpotsController@store');
		Route::post('{id}/photos/upload', ['uses'=>'SpotsController@uploadPhoto']);

		// spots
		Route::get('{id}/edit', ['uses'=>'SpotsController@edit']);
		Route::get('create', ['uses'=>'SpotsController@create']);
		Route::get('/', ['uses'=>'SpotsController@adminIndex']);
	
	});

	// ------------------------------------------------------------------------
	// Itinerary CMS
	// ------------------------------------------------------------------------
	Route::get('itinerary/', ['uses'=>'ItineraryController@adminIndex']);
	Route::get('itinerary/{id}/edit', ['uses'=>'ItineraryController@adminShow']);
	Route::get('itinerary/create', ['uses'=>'ItineraryController@create']);
	
	// ------------------------------------------------------------------------
	// Users Roles & Permissions CMS
	// ------------------------------------------------------------------------
	Route::get('users', function() {

		$users = User::where(function($q) {
			if(Input::has('office')) {
				$ids = explode(",", Input::get('office'));
				$q->whereIn('office_id', $ids);
			}
			if(Input::has('roles')) {
				$ids = explode(",", Input::get('roles'));
				$q->whereHas('roles', function($q) use($ids) {
					$q->whereIn('roles.id', $ids);
				});
			}
		})->get();

		return View::make('admin.users', ['active_link'=>'users', 'users'=>$users]);
	});

	Route::get('users/{id}', function($id) {
		return View::make('admin.edituser', ['user'=>User::find($id), 'active_link'=>'users']);
	});

	// edit a profile (*** ADMIN ONLY ***)
	Route::get('users/{id}/edit', function($id) {
		$user = User::find($id);
		if (Auth::user()->id == $user || Auth::user()->hasRole('Admin')) {
			return View::make('admin.profile', ['user'=>$user, 'active_link'=>'index']);
		}
		return 'You do not have permissions to do this...';
	});

	Route::resource('roles', 'RolesController');
	Route::resource('permissions', 'PermissionsController');
	Route::put('users/{id}', ['uses'=>'UsersController@editUserRoles']);
	

	// ------------------------------------------------------------------------
	// Office Locations TODO: put int oa OfficeController
	// ------------------------------------------------------------------------
	Route::get('offices', function() {
		return View::make('admin.offices.index');
	});

	Route::get('offices/{id}', function($id) {
		return View::make('admin.offices.edit', ['office'=>Office::find($id)]);
	});
	
	Route::post('offices', function() {
		$validate = Validator::make(Input::all(), array(
			'name'=> 'required',
			'place_id'=>'unique:locations,place_id',
			'lat'=> 'required',
			'lng'=> 'required',
		));

		if($validate->fails()) {
			return Redirect::back()->with(['errors'=>$validate->errors()->all()]);			
		}
		
		$office = new Office;
		$office->save();
		
		$location = new Location;
		$location->name 	 = Input::get('name');
		$location->lat  	 = Input::get('lat');
		$location->lng  	 = Input::get('lng');
		$location->place_id  = Input::get('place_id');
		$location->save();
		
		$office->location()->save($location);
		
		return Redirect::back()->with(['office'=>$office]);
	});	

	// update / put
	Route::put('offices/{id}', function($id) {
		$office = Office::find($id);

		if($office) {
			$validate = Validator::make(Input::all(), array(
				'name'=> 'required',
				'lat'=> 'required',
				'lng'=> 'required',
			));

			if($validate->fails()) {
				return Redirect::back()->with(['errors'=>$validate->errors()->all()]);			
			}
			
			$loc = Location::findFromPlaceID( Input::get('place_id') );
			if($loc == null) {
				
				$office->location()->delete();

				$location = new Location;
				$location->name 	 = Input::get('name');
				$location->lat  	 = Input::get('lat');
				$location->lng  	 = Input::get('lng');
				$location->place_id  = Input::get('place_id');
				$location->save();
				
				$office->location()->save($location);
			}
			else {
				$loc->name 	 = Input::get('name');
				$loc->lat  	 = Input::get('lat');
				$loc->lng  	 = Input::get('lng');
				$loc->place_id  = Input::get('place_id');
				$loc->save();
			}
			
			$office->save();

			return Redirect::back()->with(['office'=>$office]);
		}
		return Redirect::back()->with(['errors'=>'No office found']);			
	});	

	// ------------------------------------------------------------------------
	// categories
	// ------------------------------------------------------------------------
	Route::get('categories', function() {
		return View::make('admin.categories.index', ['active_link'=>'categories']);
	});
	Route::get('categories/{id}/edit', function($id) {
		return View::make('admin.categories.edit', ['category'=>Category::find($id), 'active_link'=>'categories']);
	});
	Route::put('categories/{id}', 'CategoriesController@update');
	Route::get('categories/{id}/delete', function($id) {
		$cat = Category::findOrFail($id);
		$name = $cat->name;
		if ($cat) {
			$cat->delete();
			return Redirect::back()->with('notice', '"'.$name.'" has been deleted.');
		}
		return Redirect::back()->with('errors', 'Error deleting category');
	});

	
	// admin only (master)
	Route::get('assets', function() {
		return View::make('admin.assets.index');
	});
	Route::get('assets/{id}/edit', function($id) {
		return View::make('admin.assets.edit', ['asset'=>Asset::find($id)]);
	});

});
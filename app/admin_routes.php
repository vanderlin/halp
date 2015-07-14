<?php

// --------------------------------------------------------------------------
// Admin / Roles
// --------------------------------------------------------------------------
 Route::group(['prefix'=>'admin', 'before'=>'auth'], function() {
	
	
	Route::get('/', function() {
		return View::make('admin.index', ['users'=>User::all(), 'active_link'=>'index']);
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
	// Users Roles & Permissions CMS
	// ------------------------------------------------------------------------
	Route::get('users', function() {
		return View::make('admin.index', ['users'=>User::all(), 'active_link'=>'index']);
	});

	Route::get('users/{id}', function($id) {
		return View::make('admin.edituser', ['user'=>User::find($id), 'active_link'=>'users']);
	});

	// edit a profile (*** ADMIN ONLY ***)
	Route::get('users/{id}/roles/edit', function($id) {
		$user = User::find($id);
		if (Auth::user()->hasRole('Admin')) {
			return View::make('admin.roles.edit-user-roles', ['user'=>$user, 'active_link'=>'index']);
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
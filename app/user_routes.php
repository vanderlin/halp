<?php 

Route::group(['prefix'=>'users'], function() {
		
	Route::get('/', ['uses'=>'UsersController@index']);		
	Route::get('{username}', ['uses'=>'UsersController@show']);
	Route::post('{id}', ['uses'=>'UsersController@update', 'as'=>'user.update']);

	Route::get('{username}/itineraries/shared', function($username) {
		$user = User::findFromData($username);
		$itineraries = $user->getSharedItineraries()->get();
		return $itineraries;
	});

	Route::get('{username}/itineraries', function($username) {
		$user = User::findFromData($username);
		return $user->itinerariesWithSpotIds();
		// ->each(function(&$item) {
		// 	$item->isMine = $item->isMine();
		// });				
		return $itineraries;

		if(Request::wantsJson()) {
			return $itineraries;
		}
		return View::make('site.itineraries.index', ['user'=>$user, 'itineraries'=>$itineraries]);
	});
	
	Route::get('{username}/itineraries/{id}', function($username, $id) {
		$user = User::findFromData($username);
		$itinerary = Itinerary::find($id);
		return View::make('site.itineraries.show', ['user'=>$user, 'itinerary'=>$itinerary]);
	});

	Route::get('{username}/itineraries/{id}/edit', function($username, $id) {
		return Redirect::to('/admin/itinerary/'.$id);
	});

	Route::get('{username}/itineraries/{id}/categories', function($username, $id) {
		
		$user = User::findFromData($username);
		$itinerary = Itinerary::find($id);
		
		// temp...
		if($itinerary == null) {
			return View::make('admin.itinerary.index', ['active_link'=>'itinerary', 'user'=>Auth::user()]);
		}
		
		return $itinerary->getAllCategories();
	});
	
});

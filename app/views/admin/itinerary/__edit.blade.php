
@extends('admin.layouts.default')


{{-- Web site Title --}}
@section('title')
  {{Config::get('config.site_name')}} | Edit {{ucfirst($itinerary->title)}}
@stop

@section('scripts')

<script type="text/javascript">
	$(document).ready(function($) {
		

		var $userSearch = $("#user-auto-search");
		var $form 		= $('#itinerary-form');

		// -------------------------------------
		$( "#sortable" ).sortable({
			axis: 'y',
			items: 'li',
			cursor: 'move',
			opacity:1,
	        placeholder: "ui-state-highlight",
	        start:function(e, ui) {
	        	$(this).css('list-style', 'none');
	        },
	        stop:function(e, ui) {
	        	post({refresh:false});
	        }
		});


		// -------------------------------------
		function getUserIDs() {
			var users = $('#users-list .list-group-item').map(function() {return $(this).data('id')}).get();
			var data = $userSearch.select2('data');
	    	for (var i = 0; i < data.length; i++) users.push(data[i].id);
	    	return users;
		}

		// -------------------------------------
		function getLocationIDs() {
			return $('.itinerary-spots-table .list-group-item').map(function() {return $(this).data('id')}).get();
		}

		// -------------------------------------
		function post(options) {
			
			options = options || {};

			var defaults = {
				refresh:true,
				users:getUserIDs().concat(options.users||[]),
				locations:getLocationIDs().concat(options.locations||[]),
			}


			options.locations = [];
			options.users = [];

			
			var url     = $form.attr('action');
			var fd      = new FormData($form[0]);
			
			var options = $.extend( true, defaults, options );
			
			if(options.status!=undefined) {
				fd.append('status', options.status);
			}
			for (var i = 0; i < options.users.length; i++) {
				fd.append('users[]', options.users[i]);
			};

			for (var i = 0; i < options.locations.length; i++) {
				fd.append('locations[]', options.locations[i]);
			};
			if(options.locations.length == 0) {
				fd.append('locations', null); 
			}

			

			fd.append('private', $('input[id="private"]').is(':checked'));

			if(options.place != undefined) {
				fd.append('place_name', options.place.name);
				fd.append('place_lat', options.place.geometry.location.lat());
				fd.append('place_lng', options.place.geometry.location.lng());
				fd.append('place_id', options.place.place_id);
				fd.append('place_details',  JSON.stringify(options.place));
			}
			
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data:fd,
				processData: false,
				contentType: false
			})
			.always(function(e) {
				if(options.refresh) document.location.reload();
				$("#form-status").formStatus(e, {
					fadeOut:true, 
					onDone:function(e) {
					}
				});
				console.log(e);
				if(options.onComplete) {
					options.onComplete(e);
				}
			});
		}

		// -------------------------------------
		$("#location-finder-alerts").hide();
		$("#location-finder").googleLocationFinder({
			map:"google-map-finder",
			placeChanged: function(event, data) {

			},
			onAddSpot:function(event, data) {
				$(data.element).prop('disabled', true);
				var spot = data.spot;
				if(canAddSpotToItinerary(spot)) {
					post({locations:[parseInt(spot.location.id)], refresh:true})
				}
			},
			onAddLocation:function(event, data) {
				$(data.element).prop('disabled', true);
				var place = data.place;
				if(canAddLoctionToItinerary(place)) {
					post({
						place:place,
						refresh:true,
					});
				}
			}
		});

		// -------------------------------------
		$("#itinerary-publish-btn").click(function(e) {
			e.preventDefault();
			post({
				refresh:false,
				status:'Publish',
				onComplete:function(e) {
					document.location = e.itinerary.url;
				}
			});
		});

		$("#draft-btn").click(function(e) {
			e.preventDefault();
			post({
				element:$(this), 
				status:'Draft',
				refresh:false,
				onComplete:function(e) {
					document.location = '/admin/itinerary/?updated='+e.itinerary.id;
				}
			});
		});
		


		// -------------------------------------
		$("#itinerary-delete-btn").click(function(e) {
			e.preventDefault();
			var c = confirm("Are you sure?");
			if(c) {
				$.ajax({
					url: '{{$itinerary->getPostURL()}}',
					type: 'POST',
					dataType: 'json',
					data: {_method:'DELETE'}
				})
				.done(function(evt) {
					console.log("success", evt);
					document.location = '/admin/itinerary';
				})
				.fail(function(evt) {
					console.log("error", evt);
				});
			}
		});		

		// -------------------------------------
		$('#itinerary-restore-btn').click(function(e) {
			e.preventDefault();
			post({
				element:$(this), 
				status:'Draft',
				refresh:true
			});
		});

		// -------------------------------------
		$("#preview-btn").click(function(e) {
			e.preventDefault();
			console.log('Preview');
			post({
				element:$(this), 
				refresh:false,
				onComplete:function(e) {
					document.location = e.itinerary.url;
				}
			});
		});
		// -------------------------------------
		$(".itinerary-remove-location").click(function(e) {
			e.preventDefault();	
			var id = $(this).data('id');
			$('.itinerary-spots-table .list-group-item[data-id="'+id+'"').fadeOut(300, function() {
				$(this).remove();
				post({refresh:false});
			});
		});
	
		// -------------------------------------
		$(".itinerary-remove-user").click(function(e) {
			e.preventDefault();	
			var userID = $(this).data('id');
			$('#users-list .list-group-item[data-id="'+userID+'"]').fadeOut(200, function() {
				$(this).remove();
				post({refresh:false});
			});
		});

		// -------------------------------------
		$userSearch.select2({
    		placeholder: "Search for a user",
    		minimumInputLength: 1,
    		multiple:true,
			ajax: {
	        	url: function(term) {
	        		console.log(term);
	        		return "/api/search/users/"+term;
	        	},
	        	type: "GET",
	        	dataType: 'json',
	        	quietMillis: 250,
	        	results: function (data) {
	        		// console.log("data",data);
	        		var results = [];
	        		if(data.status==200 && data.results.length>0) {
	            	
		        		$.each(data.results, function(index, item){
		        			if($('#users-list .list-group-item[data-id="'+item.id+'"]').length==0) {
		        				item.text = item.name;
			            		results.push(item);	
		        			}
				    	});
	        		}
     				return { results: results };
        		},
    		},

    	})
    	.on('select2-selecting', function(e) {
    		
    	})

    	// -------------------------------------
    	$("#add-users-button").click(function(e) {
    		e.preventDefault();
	    	post();
	    	$userSearch.select2("val", "");
    	});

    	// -------------------------------------
		$(".hero-image-upload").imageUpload({
			multiple:false,
			replace:true,
			dataType:'Itinerary',
			dataID:{{isset($itinerary) ? $itinerary->id:'null'}}, 
			dataPath:'assets/content/itinerary',
			uploadOnAdd:{{isset($itinerary) ? 'true':'false'}},
			property:"heroPhoto",
			error: function(e, data) {
				$("#form-status").formStatus(data);
			},
		});
		
		// -------------------------------------
		function addSpotToTable(spot) {
		
			var tr = '<tr class="item success ui-sortable-handle" data-id="'+spot.id+'">\
						<td><img width="30" src="'+spot.thumbnail_base+'/s30.jpg"></td>\
						<td>'+spot.name+'</td>\
						<td class="action-td"><a class="itinerary-remove-spots btn btn-danger btn-xs" href="#remove-spot" data-id="'+spot.id+'">Remove</a></td>\
					  </tr>';

			var $tr = $(tr);
			$(".itinerary-spots-table tbody").prepend($tr);
			
			$tr.fadeOut(0, 0).delay(100).fadeIn(300, function() {
				var t = $(this);
				setTimeout(function() {
					t.removeClass('success');
				}, 1200);
			})

			updateTableSize();
			
		}

		// -------------------------------------
		function addLocationToTable(place) {
			// var imgURL = $.get('/api/google/static?lat='+place.geometry.location.lat()+'&lng='+place.geometry.location.lng()+'&w=180&h=180', function(data) {
			// 	var tr = '<tr>\
			// 				<td><img width="80" height="80" src="'+data+'"></td>\
			// 				<td>\
			// 					<ul class="list-unstyled">\
			// 						<li><b>'+place.name+'</b></li>\
			// 						<li><small>'+place.formatted_address+'</small></li>\
			// 					</ul>\
			// 				</td>\
			// 				<td class="action-td"><a class="itinerary-remove-spots btn btn-danger btn-xs" href="#remove-spot" data-id="id">Remove</a></td>\
			// 			</tr>';
			// 	var $tr = $(tr).fadeOut();
			// 	$(".itinerary-spots-table tbody").append($tr);
			// 	$tr.fadeOut(0,0);
			// 	$tr.fadeIn(500);
			// });
		}

		// -------------------------------------
		function canAddSpotToItinerary(spot) {
			$exists = $('.itinerary-spots-table .list-group-item[data-type="Spot"][data-id="'+spot.id+'"]');
			if($exists.length == 0) {
				return true;
			}
			
			alert(spot.name+" is already in itinerary");
			return false;
		}

		// -------------------------------------
		function canAddLoctionToItinerary(place) {
			$exists = $('.itinerary-spots-table .list-group-item[data-type="Location"][data-place-id="'+place.place_id+'"]');
			if($exists.length == 0) {
				return true;
			}
			alert(place.name+" is already in itinerary");
			return false;
		}

		
		
	});
</script>
@stop


{{-- Content --}}
@section('content')


<div class="page-header">
    <h2 class="inline">
    	@if ($itinerary->isMine()==false)
		<img data-no-retina src="{{common_asset('icons/shared.svg')}}" data-toggle="tooltip" data-placement="top" title="Shared">
    	@endif
    	{{ $itinerary->title }}
    </h2>
    <h5 class="inline">
    	<a href="{{$itinerary->getURL()}}">view</a> | 
    	{{ link_to('admin/itinerary', 'Back to itineraries') }}
	</h5>
</div>


{{Form::open(['url'=>$itinerary->getPostURL(), 'method'=>'PUT', 'id'=>'itinerary-form'])}}
<div class="col-md-12">
  	<div class="row">

  		<div class="row">
			
			<div class="col-sm-6">		

				<!-- publish and delete -->
				{{-- -------------------------------------------------------------------------- --}}
				<div class="panel panel-default">
					<div class="panel-heading">
						@if ($itinerary->isMine()==false)
							<h3 class="panel-title pull-right">
								Created By: 
								{{--@include('site.partials.user-image', array('user' => $itinerary->user, 'size'=>'s20'))--}}
								{{link_to($itinerary->user->getProfileURL(), $itinerary->user->getName())}}							
							</h3>
						@endif
						 <h3 class="panel-title">Publish</h3>
					</div>

					<div class="panel-body" id="publish-panel">

						<div class="row">
							<div class="col-md-12">
								@if (isset($itinerary) && $itinerary->isTrashed()==false)
								<div class="pull-left">
									<button class="btn btn-default" id="draft-btn">{{isset($itinerary)&&$itinerary->status!='Draft'?'Save as Draft':'Save Draft'}}</button>
								</div>
								<div class="pull-right">
									<button class="btn btn-default" id="preview-btn">Preview</button>
								</div>
								@endif
							</div>
						</div>
						<br>

						<div class="row">
							<div class="col-md-12">
								<div class="pull-left">
									@if (isset($itinerary) && $itinerary->isFavorites()===false)
										@if ($itinerary->isTrashed())
											<a id="itinerary-restore-btn" data-id="{{$itinerary->id}}" class="btn btn-info btn-default">Remove from Trash</a>
										@else 
											<a id="itinerary-delete-btn" data-id="{{$itinerary->id}}" class="pull-right btn btn-danger btn-default" {{$itinerary->isMine()==false?'disabled':''}}>Delete</a>
										@endif
									@endif
								</div>
								
								<div class="pull-right">
									@if (isset($itinerary) && $itinerary->isTrashed()==false)
										<button id="itinerary-publish-btn" class="btn btn-default btn-success">{{empty($itinerary) || isset($itinerary)&&$itinerary->status=='Draft' ? 'Publish' : 'Update'}}</button>
									@endif
								</div>
							</div>
						</div>

						

						
						<div class="text-center form-response" id="form-status">
							@include('site.partials.form-errors')
						</div>		

					</div>

					<div class="list-group">
						<div class="list-group-item">
							<div class="checkbox">
								<label>
									{{-- we are using id due to checkboxes sucking... --}}
									<input id="private" type="checkbox" {{(isset($itinerary) && $itinerary->isPrivate()) ? 'checked':''}}> Private
								</label>
							</div>
						</div>
					</div>

				</div>
				{{-- -------------------------------------------------------------------------- --}}
	
				<!-- Hero Photo -->
				{{-- -------------------------------------------------------------------------- --}}
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h3 class="panel-title">Hero Photo</h3>
					</div>

					
					@if (isset($itinerary) && $itinerary->hasHeroImage() == false)
					<div class="panel-body" id="hero-photo-panel">
						<button class="btn btn-default hero-image-upload" type="button" data-preview-target="#hero-filelist">Add Image</button>
					</div>
					@endif
					
					<!-- List group -->
					<ul class="list-group files-list" id="hero-filelist">
						@if (isset($itinerary) && $itinerary->hasHeroImage())
							@include('admin.partials.photo-list-item', array('id' => $itinerary->id, 'image'=>$itinerary->heroPhoto, 'title'=>$itinerary->heroPhoto->getName(), 'multiple'=>false, 'target'=>'hero-image-upload', 'preview'=>'#hero-filelist'))						
						@endif
					</ul>
				</div>
				{{-- -------------------------------------------------------------------------- --}}


				<!-- Details -->
				{{-- -------------------------------------------------------------------------- --}}
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h3 class="panel-title">Itinerary Details</h3>
					</div>

					<div class="panel-body" id="spots-locations-panel">
				
					  	<!-- title -->
			          	<div class="form-group">
				            <label for="title">Title</label>
				            <input id="title" name="title" class="form-control" value="{{$itinerary->title}}" {{$itinerary->isFavorites()?'disabled':''}}>
				            <small class="help-block">slug: {{$itinerary->slug}}</small>
				        </div>

				        <!-- description -->
				        <div class="form-group description-group">
				    		<label for="name">Description</label>
				    		<textarea class="form-control textarea-editor" type="text" name="description" rows="5">{{ isset($itinerary)?$itinerary->description:Input::old('description') }}</textarea>
						</div>
				    
					</div>
				</div>
				{{-- -------------------------------------------------------------------------- --}}

				<!-- People -->
				{{-- -------------------------------------------------------------------------- --}}
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h3 class="panel-title">Collaborate with others</h3>
					</div>

					<div class="panel-body" id="people-panel">
						<div class="form-group">
							<div class="input-group">
								<input id="user-auto-search" multiple="false" type="hidden" class="form-control" placeholder="Search for users to add...">
								<span class="input-group-btn">
									<button class="btn btn-default" id="add-users-button" type="button">Add</button>
								</span>
							</div>
				        </div>
				    </div>

				    <div class="list-group table-list-group" id="users-list">
				    	@foreach ($itinerary->users as $user)
				    		@include('admin.itinerary.user-item', array('user' => $user))
				    	@endforeach
				    </div>
				</div>
				{{-- -------------------------------------------------------------------------- --}}

			</div>

			<div class="col-md-6">			
			
				<!-- Spots and Location -->
				{{-- -------------------------------------------------------------------------- --}}
				<div class="panel panel-default">
					<div class="panel-heading">
						 <h3 class="panel-title">Spots &amp; Locations</h3>
					</div>

					<div class="panel-body" id="spots-locations-panel">
						<div class="form-group">
				    		<input id="location-finder" name="google-locations" class="form-control" placeholder="Search for a spot or location">
						</div>
						<div class="form-group text-right"><a href="#close-map" class="btn btn-default btn-xs" data-toggle="collapse" data-target="#google-map-collapse">Hide Map</a></div>
						<div class="form-group collapse in" id="google-map-collapse">
							<div class="google-map" id="google-map-finder"></div>
						</div>
						<div class="alert bg-success text-center" id="location-finder-alerts"></div>
					</div>

					<ul class="list-group table-list-group itinerary-spots-table" id="sortable">
						
						@foreach ($locations as $item)
							@include('admin.itinerary.location-spot-item', array('item' => $item))
						@endforeach
					</ul>
				</div>
				{{-- -------------------------------------------------------------------------- --}}

			</div>

		</div>
  	</div>
</div>
{{Form::close()}}



@stop

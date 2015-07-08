<?php $debug = Input::get('debug', false) ?>

{{Form::open(['url'=>isset($spot)?'/admin/spots/'.$spot->id:'/admin/spots', 'method'=>isset($spot)?'PUT':'POST', 'files'=>true, 'id'=>'spot-form', 'class'=>'spot-form'])}}

<!-- Large modal -->
<div class="modal fade category-modal" tabindex="-1" role="dialog" aria-labelledby="category-modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>


<div class="row">

	<div class="col-sm-6">			
		
		{{-- -------------------------------------------------------------------------- --}}
		<div class="panel panel-default">
			<div class="panel-heading">
				 <h3 class="panel-title">Search</h3>
			</div>

			<div class="panel-body" id="search-panel">
				<div class="form-group">
					<input 	id="location-finder" 
							name="locations" 
							class="form-control" 
							placeholder="{{isset($spot)?'Update spot location':'Search for spot...'}}"  
							value="{{Input::old('google-name')}}" >

					@if ($errors->has('location_lat')) 
			    		<p class="help-block pink-color">The Location is required</p>
			    	@endif
				</div>
		    	<p class="help-block pink-color search-help"></p>
				
				<div class="form-group">
					<div class="google-map" id="google-map-finder"></div>
				</div>

				@if (isset($spot) && $spot->hasLocation())
				<div class="form-group">		
					<a 	href="" 
						data-id="{{$spot->location->id}}"
						class="btn btn-default btn-xs reload-google-details" 
						data-toggle="popover" 
						data-content="This is a button for reload the Google data from their servers. This is only here for the Beta release of Locals Only. Clicking this button helps us fix bugs and new changes to Location data."
						data-trigger="hover">Reload Google Details</a>			
				</div>
				@endif
			</div>
		</div>
		
		{{-- -------------------------------------------------------------------------- --}}
		{{-- Hidden Fields --}}
		{{-- -------------------------------------------------------------------------- --}}
		<div class="form-group">
			@if ($debug)
				<h5>Hidden Fields</h5>
			@endif 
			<ul class="list-inline">
			<li><input type="{{$debug?'text':'hidden'}}" name="details" value="{{{ isset($spot)?$spot->location->getRawDetails() : Input::old('details') }}}"></li>
			<li><input type="{{$debug?'text':'hidden'}}" name="place_id" value="{{{ isset($spot)?$spot->location->place_id : Input::old('place_id') }}}"></li>
			
			<li><input type="{{$debug?'text':'hidden'}}" name="user_id" value="{{Auth::id()}}"> </li>
			<li><input type="{{$debug?'text':'hidden'}}" name="location_name" value="{{{ isset($spot)?$spot->location->name : Input::old('google-name') }}}"></li>

			<li><input type="{{$debug?'text':'hidden'}}" name="location_lat" value="{{{ isset($spot)?$spot->location->lat : Input::old('location_lat') }}}"> </li>
			<li><input type="{{$debug?'text':'hidden'}}" name="location_lng" value="{{{ isset($spot)?$spot->location->lng : Input::old('location_lng') }}}"></li>
			@if (isset($spot) && $debug)
				<li>Loction ID: {{$spot->location->id}}</li>
				<li class="col-md-12">
					<pre>
					<small>
						<?php 
						print_r($spot->location->details) ?>
						</small>
					</pre>
				</li>
			@endif
			</ul>
		</div>
		{{-- -------------------------------------------------------------------------- --}}


	</div>



	<div class="col-sm-6">			
		
		<!-- publish and delete -->
		<div class="panel panel-default">
			<div class="panel-heading">
				 <small class="text-muted pull-right">
				 	@if (isset($spot))
				 		{{$spot->created_at->toFormattedDateString()}}
				 	@endif
				 </small>		
				 <h3 class="panel-title">Publish</h3>
			</div>

			<div class="panel-body" id="publish-panel">

				<div class="row">
					<div class="col-md-12">
						<div class="pull-left">
							<button class="btn btn-default {{isset($spot)&&$spot->status!='Draft'?'text-muted':''}}" id="draft-btn">{{isset($spot)&&$spot->status!='Draft'?'Revert to Draft':'Save Draft'}}</button>
						</div>
						<div class="pull-right">
							<button class="btn btn-default" id="preview-btn" data-id="{{isset($spot)?$spot->id:''}}">Preview</button>
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-12">
						<div class="pull-left">
							@if (isset($spot))
								@if ($spot->isTrashed())
									<a id="spot-restore-btn" data-id="{{$spot->id}}" class="btn btn-info btn-default">Remove from Trash</a>
								@else 
									<a id="spot-delete-btn" data-id="{{$spot->id}}" class="pull-right btn btn-danger btn-default">Delete</a>
								@endif
							@endif
						</div>
						<div class="pull-right">
							<button id="spot-publish-btn" class="btn btn-default btn-success">{{empty($spot) || isset($spot)&&$spot->status=='Draft' ? 'Publish' : 'Save'}}</button>
						</div>
					</div>
				</div>

				<div class="text-center form-response" id="form-status">
					@include('site.partials.form-errors')
				</div>	

			</div>
		</div>

		<!-- Spot Details -->
		<div class="panel panel-default">
			<div class="panel-heading">
				 <h3 class="panel-title">Spot Details</h3>
			</div>

			<div class="panel-body" id="details-panel">
				
				<!-- Spot Name -->
				<div class="form-group name-group {{echo_form_error('name', $errors)}}">
			    	<label for="name">Spot Name</label>
			    	<input class="form-control" autocomplete="off" type="text" name="name" value="{{ isset($spot)?$spot->name:Input::old('name') }}">
			    	@if ($errors->has('name')) 
			    		<p class="help-block pink-color">{{ $errors->first('name') }}</p>
			    	@endif
				</div>			

				<!-- its a -->
				<div class="form-group its_a-group {{echo_form_error('its_a', $errors)}}">
			    	<label for="name">Its a...</label>
			    	<input class="form-control" autocomplete="off" type="text" name="its_a" placeholder="ie: Coffee Shop" value="{{ isset($spot)?$spot->type:Input::old('its_a') }}">
			    	@if ($errors->has('its_a')) 
			    		<p class="help-block pink-color">{{ $errors->first('its_a') }}</p>
			    	@endif
				</div>

				<!-- description -->
				<div class="form-group description-group {{echo_form_error('description', $errors)}}">
			    	<label for="name">But really its...</label>
			    	<textarea class="form-control textarea-editor" type="text" name="description" id="spot-description-textarea" rows="5" placeholder="What's inspiring about this?">{{ isset($spot)?$spot->description:Input::old('description') }}</textarea>
			    	@if ($errors->has('description')) 
			    		<p class="help-block pink-color">{{ $errors->first('description') }}</p>
			    	@endif
			    	<p class="help-block char-count pull-right"></p>
				</div>

			</div>
		</div>

		<!-- photos -->
		<div class="panel panel-default">
			<div class="panel-heading">
				 <div>
				 	<small class="pull-right">Drag and drop to re-order</small>
				 	<h3 class="panel-title">Photos</h3>

				 </div>

			</div>
			
			@if (empty($post) || (isset($post) && $post->hasHero()==false))
				<div class="panel-body" id="hero-panel">
					<button class="btn btn-default" id="photos-upload-btn" type="button" data-preview-target="#photos-filelist">Add Photos</button>
				</div>
			@endif

	
			<!-- List group -->
			<ul class="list-group files-list" id="photos-filelist">
				@if (isset($spot) && $spot->photos && count($spot->photos)>0)
					@foreach ($spot->photos as $photo)

							@include('admin.partials.photo-list-item', array(
							'id' => $photo->id, 
							'image'=>$photo, 
							'title'=>$photo->getName(), 
							'replace'=>false, 
							'preview'=>'#hero-filelist'))						


							{{--<li class="list-group-item" data-id="{{$photo->id}}">
							<div class="media">

								<div class="media-left media-middle">
									<a href="{{$photo->url('w1024', false)}}" class="lightbox-link">
										<img width="40px" src="{{$photo->url('s40')}}">
									</a>
    							</div>
    							<div class="media-body media-middle">
									<div class="file-title hidden-md">{{$photo->getName()}}</div>
								</div>	

								<div class="media-right remove-photo media-middle">
			                		<a href="#delete-photo" data-id="{{$photo->id}}" data-target="#photos-filelist" class="btn btn-default btn-xs delete-photo-btn">delete</a>
			                	</div>

							</div>
						</li>--}}
					@endforeach
				@endif
			</ul>
				
		</div>

		<!-- categories -->
		<div class="panel panel-default">
			<div class="panel-heading">
				 <h3 class="panel-title">Categories</h3>
			</div>

			<div class="panel-body" id="categories-panel">
				<div class="form-group">
					<div class="category-group">
	    				@if ($errors->has('category')) 
				    		<p class="help-block pink-color">{{ $errors->first('category') }}</p>
				    	@endif
						@include('admin.spots.categories-list', ['object'=>isset($spot)?$spot:null])
					</div>
				</div>
			</div>

		</div>		

		<!-- tags -->
		<div class="panel panel-default">
			<div class="panel-heading">
				 <h3 class="panel-title">Tags</h3>
			</div>

			<div class="panel-body" id="tags-panel">
			    <div class="form-group">
					<div class="form-group tags-group {{echo_form_error('tags', $errors)}}">
				    	<?php $tagids = isset($spot) ? $spot->tagIds() : [];?>
			  			<select class="form-control auto-select" name="tags[]" multiple="multiple">
							@foreach ($tags as $tag)
								<option value="{{$tag->id}}" {{in_array($tag->id, $tagids)?'selected':''}}>{{$tag->name}}</option>
							@endforeach
						</select>

				    	@if ($errors->has('tags')) 
				    		<p class="help-block pink-color">{{ $errors->first('tags') }}</p>
				    	@endif
					</div>
				</div>		
			</div>
		</div>
		


	</div>


</div>
{{Form::close()}}


















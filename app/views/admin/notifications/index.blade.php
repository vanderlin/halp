@extends('admin.layouts.default')


{{-- Web site Title --}}
@section('title')
  Admin | Notifications 
@stop


@section('scripts')

@stop

{{-- Content --}}
@section('content')
  
  <h2 class="page-header">Site Notifications</h2>

  <div class="row">
	<div class="col-md-6">
			
		<div class="well">
	      {{Form::open(['url'=>'/admin/notifications', 'method'=>'POST', 'id'=>'create-form'])}}
	      
	       	<input type="hidden" value="{{Auth::user()->id}}" name="from_user_id">
			<div class="form-group">
				<input type="text" value="{{Input::old('slug')}}" name="slug" class="form-control" placeholder="Unique slug to reference">
			</div>

			<div class="form-group">
				<select class="form-control" name="event">
		       		<option value="{{Notification::NOTIFICATION_SITE_NOTICE}}">{{Notification::NOTIFICATION_SITE_NOTICE}}</option>
		       		<option value="{{Notification::NOTIFICATION_SITE_ERROR}}">{{Notification::NOTIFICATION_SITE_ERROR}}</option>
		       	</select>
	       	</div>

	        <!-- message -->
	        <div class="form-group">
	          	<label for="name">Message</label>
	        	<textarea class="form-control" name="message"></textarea>
	        </div>

	        <!-- update -->
	      	<div class="form-group">
	        	<button type="submit" form="create-form" class="btn btn-default">Create</button>
	      	</div>
	      	{{Form::close()}}

	      	<div class="text-center">
	        	@include('site.partials.form-errors')
	    	</div>
  		</div>
  	

  	@if ($notifications->count()==0)
  		<div class="text-center">
  			<small>No Notifications</small>
  		</div>
  	@else
	    <table class="table table-striped">
	    
	    	<thead>
		        <tr>
		          <th>#</th>
		          <th>Name</th>
		          <th>Slug</th>
		        </tr>
	    	</thead>

	      	<tbody>
		        @foreach ($notifications as $notice)
		       		<tr>
			            <td>{{ $notice->id }}</td>
			            <td>{{ $notice->slug }}</td>
		          	</tr>
		        @endforeach
	      	</tbody>
	    </table>
    @endif

  </div>
</div>
@stop

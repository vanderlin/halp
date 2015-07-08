@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | {{$user->getName()}}	
@stop


@section('scripts')

@stop

{{-- Content --}}
@section('content')

<div class="page-header">
	<h2 class="inline">{{$user->getName()}}</h2>	
	<h5 class="inline"><a href="{{$user->getProfileURL()}}">view</a></h5>
</div>

<div>
	
	<ul class="list-inline">
		<li><h5 class="text-muted">{{$user->getRoleName()}}</h5></li>
		<li><h5><a href="{{URL::to('users/logout')}}">Sign Out</a></h5></li>
	</ul>
	

</div>

{{ Form::open([ 'route'=>['user.update', $user->id], 
				'method'=>'PUT',
				'id'=>'user-update-form',
				'role'=>'form']) }}

<div class="well">
	<fieldset>
		
		<div class="form-group text-center">
			<div id="profile-image-container">
				<ul class="list-unstyled">
				<li><img src="{{ $user->profileImage->url('s150') }}" class="img-circle profile-image"></li>
				<li><h5>{{$user->getRoleName()}}</h5></li>
				</ul>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="username">Username</label>
			    	<input type="text" class="form-control" id="username" placeholder="Username" value="{{$user->username}}" disabled>
				</div>

				<div class="form-group">
					<label for="email">Email</label>
					<input disabled type="email" class="form-control" id="email" name="email" placeholder="example@website.com" value="{{$user->email}}" {{Auth::user()->hasRole('Admin')?'':'disabled'}}>
				</div>
				
				<div class="form-group">
					<label for="firstname">First Name</label>
					<input autocomplete="off" class="form-control" placeholder="First Name" type="text" name="firstname" id="firstname" value="{{$user->firstname}}">
				</div>

				<div class="form-group">
					<label for="lastname">Last Name</label>
					<input autocomplete="off" class="form-control" placeholder="Last Name" type="text" name="lastname" id="lastname" value="{{$user->lastname}}">
				</div>

			</div>

			<div class="col-md-6">
				

				<div class="form-group">
					<label for="lastname">At IDEO I am called a...</label>
					<input autocomplete="off" class="form-control" placeholder="ie:Design Researcher" type="text" name="discipline" id="discipline" value="{{$user->discipline}}">
				</div>

				<div class="form-group">
					<label for="lastname">Outside IDEO I am called a...</label>
					<input autocomplete="off" class="form-control" placeholder="ie:Surfer, Coffee Drinker" type="text" name="hobby" id="hobby" value="{{$user->hobby}}">
					<!-- <br><small>You can add many with commas</small> -->
				</div>

				<div class="form-group">
					<label for="location">Office Location</label>
					<select class="form-control" name="office_location">
						@foreach (Office::all() as $office)
							<option {{$user->location->id==$office->id?'selected':''}} value="{{$office->id}}">{{$office->name}}</option>
						@endforeach
					</select>
				</div>
			</div>	
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="form-group text-right">
					<button type="submit" class="btn btn-default" form="user-update-form">Update</button>
					<a href="{{URL::to('users/'.$user->id.'/google_update')}}" class="btn btn-default btn-info" form="user-update-form">Update Google Info</a>
				</div>

				<div class="form-group text-center">
					@include('site.partials.form-errors')
				<div>
			</div>
		</div>


		</div>


	</fieldset>
</div>

{{ Form::close() }}

@stop


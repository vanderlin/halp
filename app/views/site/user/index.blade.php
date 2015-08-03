@extends('site.layouts.default', ['use_footer'=>true])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Leaderboard
@stop

@section('scripts')

@stop




{{-- Content --}}
@section('content')

<section class="hero bgcolor2">
	<h3 class="light-h3">The most helpful person is:</h3>
		<div class="user-image-most-helpful">
			<img src="{{asset('assets/img/bird-left.svg')}}" class="bird-left">
			<a href="{{$leader->getProfileURL()}}">
				<img src="{{$leader->profileImage->url('s280')}}">
			</a>
			<img src="{{asset('assets/img/bird-right.svg')}}" class="bird-right">
		</div>
		<div class="user-details">
			<span class="user-name-leaderboard">
				<h5>{{$leader->getName()}}</h5>
			</span>
			<span class="total-task">
				<h4>{{$leader->totalClaimed()}} Claimed Tasks!</h4>
			</span>
			<hr>
		</div>
</section>

<section class="weekly-awards">
		<div class="award">
			<img class="headshot" src="{{$leader->profileImage->url('s113')}}">
			<img src="{{asset('assets/img/award-last-week.svg')}}" class="award-image">
			<h6>Most Helpful Last Week</h6>
			<hr>
			<h4><a href="#">Person's Name</a></h4>
			<h5>XX Claimed Tasks</h5>
		</div>
		<div class="award">
			<img class="headshot" src="{{$leader->profileImage->url('s113')}}">
			<img src="{{asset('assets/img/award-project.svg')}}" class="award-image">
			<h6>Most Active Project</h6>
			<hr>
			<h4><a href="#">Project Name</a></h4>
			<h5>Created by <a href="#">Person Name</a></h5>
		</div>
		<div class="award">
			<img class="headshot" src="{{$leader->profileImage->url('s113')}}">
			<img src="{{asset('assets/img/award-most-created.svg')}}" class="award-image">
			<h6>Most Tasks Created</h6>
			<hr>
			<h4><a href="#">Person's Name</a></h4>
			<h5>XX Tasks Created</h5>
		</div>
</section>

<section class="content">
	@if ($users->count()>0)
		@foreach ($users as $user)
			@include('site.user.leaderboard-card', array('user' => $user))
		@endforeach
		{{--$users->links()--}}
	@else
		<h3>No Users</h3>
	@endif
</section>
@stop

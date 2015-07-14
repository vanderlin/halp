@extends('site.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Leaderboard
@stop

@section('scripts')

@stop




{{-- Content --}}
@section('content')

<section class="hero">
	<h3>The most helpful person is:</h3>
		<div class="user-image-most-helpful">
			<a href="{{$leader->getProfileURL()}}">
				<img src="{{$leader->profileImage->url('s280')}}">
			</a>
		</div>
		<div class="user-details">
			<span class="user-name">
				<h5>{{$leader->getName()}}</h5>
			</span>
			<span class="total-task">
				<h4>{{$leader->totalClaimed()}} Completed Tasks!</h4>
			</span>
			<hr>
		</div>
</section>

<section class="content">
@forelse ($users as $user)
	@include('site.user.leaderboard-card', array('user' => $user))
@empty
	<h3>No Users</h3>
@endforelse
</section>
@stop

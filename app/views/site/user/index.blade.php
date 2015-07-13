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
		<div class="user-image">
			<img src="{{$leader->profileImage->url('s72')}}">
		</div>
		<div class="user-details">
			<span class="user-name">{{$leader->getName()}}</span>
			<span class="total-task">
				<h3>{{$leader->totalClaimed()}} Completed Tasks!</h3>
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

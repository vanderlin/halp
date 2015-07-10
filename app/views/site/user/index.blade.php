@extends('site.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Leaderboard
@stop

@section('scripts')

@stop




{{-- Content --}}
@section('content')
<section class="content">
<h1>Users</h1>
@forelse ($users as $user)

	<div class="task">
		<div class="task-details">
			<span class="task-name">{{link_to($user->getProfileURL(), $user->getName())}}</span>
			<hr>
			<img src="{{$user->profileImage->url('s100')}}" class="img-circle">
			<span class="project-name">Total Tasks: {{$user->getTotalTask()}}</span>
			<span class="date">{{$user->created_at->toFormattedDateString()}}</span>
		</div>
		<div class="posted-by">{{$user->email}}</div>
	</div>
	
@empty
	<h3>No Users</h3>
@endforelse
</section>
@stop

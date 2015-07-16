@extends('site.layouts.default', ['use_navigation'=>true, 'use_footer'=>false])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}} | {{$user->getName()}}
@stop

@section('head')
@stop

@section('scripts')
@stop


@section('content')
		
	<section class="hero">
		@if (Auth::check() && Auth::id() == $user->id)
			<h3>{{$user->getName()}}</h3>
			@if($user->hasRole('Admin'))
				<small>{{link_to('admin', 'Admin')}}</small>
			@endif
			<hr>
			<p>E-mail me when a new task is added to Halp:</p>
			<a href="#yes"><img class="check-box" src="{{img('circle_check.svg')}}"></a>
			<h5>Yes Please</h5>
		@else
			<div class="user-image-most-helpful">
				<a href="{{$user->getProfileURL()}}">
					<img src="{{$user->profileImage->url('s280')}}">
				</a>
			</div>
			<h3>{{$user->getName()}}</h3>
		@endif
	</section>

	<section class="tasks user-tasks claimed-task-section">
		<div class="line-break">
			<h2>{{$user->totalClaimed()}} Claimed Tasks!</h2>
		</div>
		@forelse ($user->claimedTasks as $task)
			@include('site.tasks.claimed-task', array('task' => $task))
		@empty
			<br/><h5>It's time to start helping.</h5>
		@endforelse
	</section>
	
	<br><br>

	<section class="tasks user-tasks">
		<div class="line-break">
			<h2>You have created {{$user->totalCreated()}} Tasks!</h2>
		</div>
		@forelse ($user->createdTasks as $task)
			@include('site.tasks.claimed-task', array('task' => $task))
		@empty
			<br/><h5>Go make some {{link_to('/', 'tasks')}}</h5>
		@endforelse
	</section>
@stop
    

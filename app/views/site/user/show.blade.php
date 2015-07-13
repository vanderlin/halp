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
		<h3>{{$user->getName()}}</h3>
		<hr>
		<p>E-mail me when a new task is added to Halp:</p>
		<a href="#yes"><img src="{{img('circle_check.svg')}}"></a>
		<h5>Yes Please</h5>
	</section>

	<section class="tasks">
		<h2>{{$user->totalClaimed()}} Completed Tasks!</h2>
		@forelse ($user->claimedTasks as $task)
			@include('site.tasks.claimed-task', array('task' => $task))
		@empty
			<h5>No Completed Tasks</h5>
		@endforelse
	</section>
@stop
    

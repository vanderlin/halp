@extends('site.layouts.default', ['use_navigation'=>true, 'use_footer'=>true, 'container_class'=>'task-show'])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}} | {{$task->title}}
@stop

@section('head')
@stop

@section('scripts')
@stop


@section('content')

		
		<div class="email-content">
			
			<div class="task-status {{$task->isClaimed?'claimed':''}}">
			Status: 
			@if ($task->isClaimed)
				<h2>Claimed</h2>
			@else
				<h2>Not Claimed</h2>
			@endif
			</div>
			
			<img src="http://halp.ideo.com/assets/img/unhappy-turtle.png" />
			<h2>Alert! Alert! {{$task->creator->getShortName()}} needs help.</h2>
			<hr>
			<h3>{{link_to($task->creator->getProfileURL(), $task->creator->firstname)}} is looking for help with:</h3>
			<h1>{{link_to($task->getURL(), $task->title)}} for {{link_to($task->project->getURL(), $task->project->title)}}</h1>
			<p>This task will take {{$task->duration}} to complete. If you think you can help, claim this task!</p>
			<div class="progress-button small">
				<button class="halp-claim-button" data-id="{{$task->id}}" data-mfp-src="/tasks/{{$task->id}}?json=true"><span>Claim task</span></button>
			</div>
		</div>
	

@stop
  



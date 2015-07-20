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
			@if ($task->isClaimed)
				<h3>Status: Claimed</h3>
			@else
				<h3>Status: Not Claimed</h3>
			@endif
			</div>
			
			<img src="http://halp.ideo.com/assets/img/unhappy-turtle.png" />
			<h2>Alert! Alert! {{$task->creator->getShortName()}} needs help.</h2>
			<hr>
			<h3>{{link_to($task->creator->getProfileURL(), $task->creator->firstname)}} is looking for help with:</h3>
			<h1>{{link_to($task->getURL(), $task->title)}} for {{link_to($task->project->getURL(), $task->project->title)}}</h1>
			<p>This task will take {{$task->duration}} to complete. If you think you can help, claim this task!</p>
			
			@if ($task->isClaimed == false)
				<div class="progress-button small">
					<button class="halp-claim-button" data-id="{{$task->id}}" data-mfp-src="/tasks/{{$task->id}}?json=true"><span>Claim task</span></button>
				</div>
			@elseif ($task->claimed_id == Auth::id())
				{{Form::open(['route'=>['tasks.unclaim', $task->id]])}}
				<div class="progress-button small">
					<button data-id="{{$task->id}}" href="#return-task"><span>Return Task</span></button>
				</div>
				{{Form::close()}}
			@else
			<hr>
				<div class="claimer">
					Claimed by: {{link_to($task->claimer->getProfileURL(), $task->claimer->getShortName())}}
					<div class="date">
						{{$task->claimed_at->diffForHumans()}}
					</div>
				</div>	
			@endif

		</div>
	

@stop
  



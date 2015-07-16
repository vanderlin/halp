@extends('site.layouts.default', ['use_navigation'=>true, 'use_footer'=>false])

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
				<img src="http://halp.ideo.com/assets/img/unhappy-turtle.png" />
				<h2>Alert! Alert! {{$task->creator->getShortName()}} needs help.</h2>
				<hr>
				<h3>{{link_to($task->creator->getProfileURL(), $task->creator->firstname)}} is looking for help with:</h3>
				<h1>{{link_to($task->getURL(), $task->title)}} for {{link_to($task->project->getURL(), $task->project->title)}}</h1>
				<p>This task will take {{$task->duration}} to complete. If you think you can help, claim the task on Halp.</p>
				<a href="{{URL::to($task->getURL())}}"><div class="rounded-button">Go to Halp</div></a>
			</div>
		</div>

@stop
  



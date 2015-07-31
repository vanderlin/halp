@extends('emails.layout')

@section('content')
	<img class="img-circle" src="{{production_url($task->creator->profileImage->url('s120'))}}" alt="{{$task->creator->getName()}}">
	<h2>{{link_to($task->creator->getProfileURL(), $task->creator->getShortName())}} no longer needs help with:</h2>
	<hr>
	<h1>{{$task->title}} for {{link_to($task->project->getURL(), $task->project->title)}}</h1>
	<p>Thanks for trying though...</p>
	<p>Go find more {{link_to('/', 'tasks!')}}</p>
@stop
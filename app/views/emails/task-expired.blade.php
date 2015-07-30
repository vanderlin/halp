@extends('emails.layout')

@section('content')
	<h2>{{link_to($task->creator->getProfileURL(), $task->creator->getShortName())}} your task expired {{$task->date->diffForHumans()}}:</h2>
	<hr>
	<h1>{{$task->title}} for {{link_to($task->project->getURL(), $task->project->title)}}</h1>
	<p>Head over to {{link_to('/', 'Halp')}} to re-post or delete your task.</p>
	<a href="{{$task->getURL()}}"><div class="rounded-button">View Task</div></a>
@stop
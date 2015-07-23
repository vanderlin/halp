@extends('emails.layout')

@section('content')
		
<h2>{{link_to($task->claimer->getProfileURL(), $task->claimer->getShortName())}} has claimed one of your tasks!</h2>
<hr>
<h3>You asked for help with:</h3>
<h1>{{link_to($task->getURL(), $task->title.' for '.$task->project->title)}}</h1>
<p>You estimated this task would take {{$task->duration}}. Go talk to {{link_to($task->claimer->getURL(), $task->claimer->firstname)}} and happy task-ing!</p>

@stop
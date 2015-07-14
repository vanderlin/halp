<div class="white-popup claimed-popup">
	<h2>Help {{$task->creator->getShortName()}} with {{ucfirst($task->title)}}</h2>
	<div class="task-message">
		<p>
			This task is for {{link_to($task->project->getURL(), $task->project->title)}}. 
			{{ucfirst($task->creator->firstname)}} estimates he will need help for {{$task->duration}}.
		</p>
	</div>
	<div class="progress-button small claimed-buttons">
		{{Form::open(['route'=>['tasks.claim', $task->id]])}}
			<button data-id="{{$task->id}}"><span>Send an E-Mail</span></button>
			<button data-id="{{$task->id}}"><span>Talk IRL</span></button>
		{{Form::close()}}
	</div>
</div>

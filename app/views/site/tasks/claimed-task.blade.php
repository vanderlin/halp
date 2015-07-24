<div class="task claimed-task task-card-{{$task->id}}">
	<div class="task-details">
		<span class="task-name">{{$task->title}}</span>
		<hr>
		<span class="project-name">For {{link_to($task->project->getURL(), $task->project->title)}}</span>
		<span class="date-claimed">{{$task->claimed_at->toFormattedDateString()}}</span>
	</div>
	@if (Auth::check() && Auth::id() == $task->claimer->id)
	<div class="return-task">
		<p>Return to the task board if you can’t complete this.</p>
		{{Form::open(['route'=>['tasks.unclaim', $task->id]])}}
		<div class="progress-button small">
			<button data-id="{{$task->id}}" href="#return-task"><span>Return Task</span></button>
		</div>
		{{Form::close()}}
	</div>
	@endif
</div>

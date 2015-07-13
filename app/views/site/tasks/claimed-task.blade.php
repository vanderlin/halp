<div class="task">
	<div class="task-details">
		<span class="task-name">{{$task->title}}</span>
		<hr>
		<span class="project-name">For {{$task->project->title}}</span>
		<span class="claimed-by">{{$task->claimed_at->toFormattedDateString()}}</span>
	</div>
</div>

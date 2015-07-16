<div class="task {{$task->isClaimed?'claimed':''}}">
	<div class="task-details">
		<span class="task-name">{{$task->title}}</span>
		<hr>
		<span class="project-name">{{link_to($task->project->getURL(), $task->project->title)}}</span>
		<span class="duration">{{$task->duration}}</span>
		<span class="date">{{$task->created_at->toFormattedDateString()}}</span>
		
		<div class="progress-button small">
			{{-- <button class="halp-claim-button" data-id="{{$task->id}}" data-mfp-src="/tasks/{{$task->id}}?json=true"><span>Edit</span></button> --}}
			<button class="halp-delete-task-button" data-id="{{$task->id}}"><span>Delete</span></button>
		</div>
	
	</div>
	@if ($task->isClaimed)
		<div class="claimed-by">Claimed by {{link_to($task->claimer->getProfileURL(), $task->claimer->getShortName())}}</div>
	@endif
</div>

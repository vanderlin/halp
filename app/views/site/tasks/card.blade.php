<div class="task {{$claimed?'claimed':''}}">
	<div class="task-details">
		<span class="task-name">{{$task->title}}</span>
		<hr>
		<span class="project-name">{{link_to($task->project->getURL(), $task->project->title)}}</span>
		<span class="duration">{{$task->duration}}</span>
		<span class="date">{{$task->created_at->toFormattedDateString()}}</span>
		@if (!$claimed)
			<div class="progress-button small">
				<button class="halp-claim-button" data-id="{{$task->id}}" data-mfp-src="/tasks/{{$task->id}}?json=true"><span>Claim task</span></button>
			</div>
		@endif
	</div>
	<div class="{{$claimed?'claimed':'posted'}}-by">{{$claimed?'Claimed':'Posted'}} by {{link_to($task->creator->getProfileURL(), $task->creator->getShortName())}}</div>
</div>

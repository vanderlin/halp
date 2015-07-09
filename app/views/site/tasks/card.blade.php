<div class="task {{$claimed?'claimed':''}}">
	<div class="task-details">
		<span class="task-name">{{$task->title}}</span>
		<hr>
		<span class="project-name">For {{$task->project->title}}</span>
		<span class="date">{{$task->duration}}</span>
		<span class="date">{{$task->created_at->toFormattedDateString()}}</span>
		@if (!$claimed)
			<div class="progress-button small">
				<button><span>Claim task</span></button>
			</div>
		@endif
	</div>
	<div class="{{$claimed?'claimed':'posted'}}-by">{{$claimed?'Claimed':'Posted'}} by {{$task->creator->getShortName()}}</div>
</div>

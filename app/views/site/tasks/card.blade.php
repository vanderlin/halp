<div class="task">
	<div class="task-details">
		<span class="task-name">{{$task->title}}</span>
		<hr>
		<span class="project-name">For {{$task->project->title}}</span>
		<span class="date">July 3, 2015</span>
		<div class="progress-button small">
			<button><span>Claim task</span></button>
		</div>
	</div>
	<div class="posted-by">Posted by {{$task->creator->getShortName()}}</div>
</div>

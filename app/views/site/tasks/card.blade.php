<?php 
$show_button = isset($show_button) ? $show_button : true;
$class = isset($class) ? $class : "";
?>
<div class="task {{$task->isClaimed?'claimed':''}} task-card-{{$task->id}} {{$class}}">
	
	@if ($task->isDueSoon())
		<div class="ribbon-wrapper"><div class="ribbon">Due Soon</div></div>
	@endif
	
	
	<div class="task-details">
	
		<span class="task-name" data-value="{{$task->title}}">{{$task->title}}</span>
		<hr>
		<span class="project-name">{{link_to($task->project->getURL(), $task->project->title)}}</span>
		<span class="duration">{{$task->duration}}</span>
		<span class="date {{$task->isExpiredAndNotClaimed() ? 'expired':''}}">
			@if($task->isExpiredAndNotClaimed()) 
				This task expired {{$task->date->diffForHumans()}}
			@else
				{{ $task->getExpirationDateForHumans()}}
				{{--$task->isDueSoon() ? $task->getExpirationDate()->diffForHumans() : $task->date->format('F j, Y')--}}
			@endif
		</span>
	
	@if ($show_button)
		@if ($task->isMine())
			<div class="progress-button small">
				<button class="halp-edit-button" data-id="{{$task->id}}"><span>Edit task</span></button>
			</div>
		@else
			<div class="progress-button small">
				<button class="halp-claim-button" data-id="{{$task->id}}" data-mfp-src="/tasks/{{$task->id}}?json=true"><span>See Details</span></button>
			</div>
		@endif
	@endif	
		
	</div>
	<div class="card-footer {{$task->isClaimed?'claimed':'posted'}}-by">
	@if ($task->isClaimed)
		Claimed by {{link_to($task->claimer->getProfileURL(), $task->claimer->getShortName())}}
	@else
		Posted by {{link_to($task->creator->getProfileURL(), $task->creator->getShortName())}}
	@endif
	
	@if ($task->isMine())
		<div class="edit-bar">
			<a class="halp-delete-task-button" href="#delete-task" data-id="{{$task->id}}" data-target=".task-card-{{$task->id}}"><i class="fa fa-trash-o"></i></a>
		</div>
	@endif
	</div>
</div>

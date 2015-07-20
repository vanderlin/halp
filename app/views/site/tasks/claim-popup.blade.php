<img src="{{asset('assets/img/front-facing-turtle.png')}}" width="132px" height="64px" class="front-facing-turtle"/>

<div class="white-popup claimed-popup animated fadeIn">
	@if ($task->isClaimed)
		<h2>sorry...</h2>
		<p>This {{link_to($task->getURL(), 'task')}} has already been claimed by {{link_to($task->claimer->getProfileURL(), $task->claimer->getName())}}</p>
		<hr>
		<div class="progress-button small">
			<button class="close-popup"><span>Close</span></button>
		</div>
	@else
		<h2>Help {{$task->creator->getShortName()}} with {{ucfirst($task->title)}}</h2>
		<hr>
		<div class="task-message">
			<p>
				This task is for {{link_to($task->project->getURL(), $task->project->title)}}. 
				{{ucfirst($task->creator->firstname)}} estimates this task will take about {{$task->duration}}.
			</p>
		</div>
		<div class="progress-button small claimed-buttons">
			{{Form::open(['route'=>['tasks.claim', $task->id], 'id'=>'claim-task-form'])}}
				<button type="submit" data-id="{{$task->id}}"><span>Claim Task</span></button>
			{{Form::close()}}
		</div>

	@endif
</div>

<img src="{{asset('assets/img/front-facing-turtle.png')}}" width="132px" height="64px" class="front-facing-turtle"/>

<div class="white-popup claimed-popup animated fadeIn">
	<div class="popup-content edit-task-content">
			
		<h2>Edit your task "<span class="task-title">{{$task->title}}</span>"</h2>
		<hr>
	
			{{Form::open(['route'=>['tasks.update', $task->id], 'method'=>'PUT', 'id'=>'edit-task-form'])}}
				
				<div class="form-field">
					<label>Name of task</label>
					<input type="text" class="validate" data-error-message="Derp!" data-required="true" data-max="{{Config::get('config.max_title')}}" autocomplete="off" name="title" id="edit-task-title" value="{{$task->title}}">
				</div> 

				<div class="form-field">
					<label>Project</label>
					<input type="text" class="validate" data-error-message="Derp!" data-validate data-required="true" name="project" id="edit-task-project" value="{{$task->project->title}}" data-project-id="{{$task->project->id}}">
				</div>

				<div class="form-field">
					<label>How long this task will take</label>
					<input type="text" class="validate" data-error-message="Derp!" data-validate data-required="true" autocomplete="off" name="duration" value="{{$task->duration}}">
				</div>


				<div class="form-field">
					<label>When will this task happen</label>
					<input type="text" name="task_date" autocomplete="off" id="edit-task-datepicker" data-default-date="{{$task->date->format('m-d-Y')}}" value="{{$task->date->format('F j, Y')}}">
				</div>

				<div class="form-field">
					<label>Details (optional)</label>
					<textarea name="details">{{$task->details}}</textarea>
				</div>
				
				<div class="progress-button small claimed-buttons">
					<button type="submit" data-id="{{$task->id}}"><span>Update Task</span></button>
				</div>

			{{Form::close()}}
		
	
	</div>
</div>

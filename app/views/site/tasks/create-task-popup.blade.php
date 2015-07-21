	<img src="{{asset('assets/img/front-facing-turtle.png')}}" width="132px" height="64px" class="front-facing-turtle"/>


	<div class="white-popup claimed-popup animated fadeIn">
		{{Form::open(['route'=>'tasks.store', 'id'=>'create-task-form'])}}
		<input type="hidden" name="title" value="{{isset($data['title'])?$data['title']:''}}">
		<input type="hidden" name="project" value="{{isset($data['project'])?$data['project']:''}}">
		<input type="hidden" name="duration" value="{{isset($data['duration'])?$data['duration']:''}}">
		<h2>Add a few more details (optional):</h2>	
		<hr>
		<div class="form-field">
			<label for="task_date">Any more details you want to add?</label>
			<input type="text" id="datepicker" value="{{Input::old('task_date')}}">
		</div>
		<div class="form-field">
			<label for="details">Any more details you want to add?</label>
			<textarea name="details">{{Input::old('details')}}</textarea>
		</div>
		<div class="progress-button small claimed-buttons">
			<button type="submit"><span>Create Task</span></button>
		</div>
		{{Form::close()}}
	</div>

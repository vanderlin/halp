
<img src="{{asset('assets/img/front-facing-turtle.png')}}" width="132px" height="64px" class="front-facing-turtle"/>

<div class="white-popup claimed-popup animated fadeIn">
	<div class="popup-content">	

		{{Form::open(['route'=>'feedback.store', 'id'=>'feedback-form'])}}
		<div class="form-field">
			<label for="details">Give us some feedback:</label>
			<textarea name="feedback" placeholder="What do you think?">{{Input::old('feedback')}}</textarea>
		</div>
		<div class="progress-button small create-task-buttons">
			<button type="submit"><span>Send</span></button>
		</div>
		{{Form::close()}}
	</div>
</div>
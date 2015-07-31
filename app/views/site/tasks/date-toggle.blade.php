<?php $date_value = isset($task) ? $task->date->format('Y-m-d'): '';?>
<div class="date-wrapper">
	<div class="selector {{isset($task) && !$task->hasSetDate() ? 'active' :''}}">
		<a href="#" id="date-none-button"><span>Whenever!</span></a>
	</div>
	<div class="form-field date-field">
		@if (isMobile())
			<input type="date" id="task-datepicker" class="{{isset($task) && $task->hasSetDate() ? 'active' :''}}" placeholder="Pick a Date" name="task_date" autocomplete="off" value="{{$date_value}}">					
		@else
			<input type="text" id="task-datepicker" class="{{isset($task) && $task->hasSetDate() ? 'active' :''}}" placeholder="Pick a Date" name="task_date" autocomplete="off" data-default-date="{{$date_value}}" value="{{$date_value}}">
		@endif	
	</div>
</div>		
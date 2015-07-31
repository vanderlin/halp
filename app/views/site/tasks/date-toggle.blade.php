<?php $date_value = isset($task) ? $task->date->format('Y-m-d'): '';?>
<div class="date-wrapper">
	<input type="hidden" name="does_not_expire" value="{{isset($task)?strbool($task->does_not_expire):''}}">
	<div class="selector {{isset($task) && $task->does_not_expire ? 'active' :''}}">
		<a href="#" id="date-none-button"><span>Whenever!</span></a>
	</div>

	<div class="form-field date-field">
		
		<input 	type="{{isMobile()?'date':'text'}}" 
				data-use-js-picker="{{isMobile()?'false':'true'}}" 
				data-default-date="{{$date_value}}" 
				id="task-datepicker" 
				class="{{isset($task) && $task->hasSetDate() ? 'active' :''}}" 
				placeholder="Pick a Date" 
				name="task_date" 
				autocomplete="off" 
				value="{{$date_value}}">					
	</div>
</div>		
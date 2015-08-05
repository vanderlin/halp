@extends('admin.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Admin | Tests
@stop

@section('scripts')
<script type="text/javascript">
jQuery(document).ready(function($) {
$('.ui.checkbox').checkbox();
});
</script>
@stop




{{-- Content --}}
@section('content')

<?php 
$users = User::take(20)->get();
$tasks = Task\Task::take(20)->get();
$eventTypes = Notification::$eventTypes;
$evts = [];
foreach ($eventTypes as $event) {
	$t = ['name'=>$event];
	array_push($evts, (object)$t);
}
$eventTypes = $evts;
?>
<section class="content container text-left">
	
	<h3>Send Email</h3>
	<table class="ui celled table">
		<thead>
			<tr>
				<th>Event</th>
				<th>Task</th>
				<th>Creator</th>
				<th>Claimer</th>
				
			</tr>
		</thead>
		<tbody>
	
			{{Form::open(['url'=>'admin/emails/send', 'method'=>'POST'])}}
				
					<tr>
				      	<td>
				      		<select class="ui dropdown" name="event">
				      		@foreach ($eventTypes as $event)
				      			<option value="{{$event->name}}"><code>{{$event->name}}</code></option>
				      		@endforeach
				      		</select>
				      	</td>
						
						<td>
							<select class="ui dropdown" name="task_id">
						    @foreach ($tasks as $task)
						    	<option value="{{$task->id}}">{{$task->title}}</option>
						    @endforeach
						    </select>
						</td>

						<td>
						    <select class="ui dropdown" name="creator_id">
						    	@foreach ($users as $user)
						    	<option value="{{$user->id}}">{{$user->getName()}}</option>
						    	@endforeach
						    </select>
						</td>

						<td>
							<select class="ui dropdown" name="claimed_id">
								<option value="NULL">NULL</option>
								@foreach ($users as $user)
							   	<option value="{{$user->id}}">{{$user->getName()}}</option>
							    @endforeach
						    </select>
						</td>

		    		</tr>

					<tr>
						<td colspan="2"></td>
		    			<td colspan="2">
							{{--<div class="ui input">
								<div class="field">
									<label>Email Subject</label>
									<input type="text" name="subject" placeholder="enter a subject">
								</div>
							</div>
		    				<div class="ui action input">
		    					<input type="text" placeholder="Where to send email..." name="emails" value="{{Auth::user()->email}}">
								<button type="submit" class="ui button">Send</button>
							</div>
							<div><small><b>comma separated list</b></small></div>
							--}}
							<div class="ui small form">
						    	<div class="field">
						      		<label>Email Subject</label>
						      		<input placeholder="optional" name="subject" type="text">
						    	</div>
						    	<div class="field">
						      		<label>Email Address <small>(comma separated list)</small></label>
						      		<input placeholder="ie:todd@gmail.com, kim@gmail.com" type="text" name="emails" value="{{Auth::user()->email}}">
						    	</div>
						    	<div class="field center aligned">
						    		<label>&nbsp;</label>
						    		<button type="submit" class="ui button">Send Email</button>
						    	</div>
							</div>
					    </td>
		    		</tr>
				
			{{Form::close()}}

		</tbody>
	</table>

	<hr>

	<h3>View Email (pre sending)</h3>
	<table class="ui celled table">
		<thead>
			<tr>
				<th>Event</th>
				<th>Task</th>
				<th>Creator</th>
				<th>Claimer</th>
			</tr>
		</thead>
		<tbody>
	
			{{Form::open(['url'=>'admin/emails/view-email', 'method'=>'GET'])}}
				
					<tr>
				      	<td>
				      		<select class="ui dropdown" name="event">
				      		@foreach ($eventTypes as $event)
				      			<option value="{{$event->name}}"><code>{{$event->name}}</code></option>
				      		@endforeach
				      		</select>
				      	</td>
						
						<td>
							<select class="ui dropdown" name="task_id">
						    @foreach ($tasks as $task)
						    	<option value="{{$task->id}}">{{$task->title}}</option>
						    @endforeach
						    </select>
						</td>

						<td>
						    <select class="ui dropdown" name="creator_id">
						    	@foreach ($users as $user)
						    	<option value="{{$user->id}}">{{$user->getName()}}</option>
						    	@endforeach
						    </select>
						</td>

						<td>
							<select class="ui dropdown" name="claimed_id">
								<option value="NULL">NULL</option>
								@foreach ($users as $user)
							   	<option value="{{$user->id}}">{{$user->getName()}}</option>
							    @endforeach
						    </select>
						</td>
		    		</tr>

    				<tr>
						<td colspan="2">
							<div class="ui small form">
								<div class="field">
									<b>Additional Parameters</b>
								</div>
								<div class="field">
									<label for="award_type">Award Types</label>
									<select class="ui dropdown" name="award_type">
										@foreach (Award::getAwards() as $award)
									   	<option value="{{$award->name}}">{{$award->name}}</option>
									    @endforeach
								    </select>
							    </div>
						    </div>
						</td>
		    			<td colspan="2">
		    				<div class="ui small form">
								<button type="submit" class="ui button">View Email</button>
								<div class="ui checkbox">
							     	<input type="checkbox" tabindex="0" checked name="pre_render" class="hidden">
							      	<label>Convert to inline styles</label>
							    </div>
						    </div>
					    </td>
		    		</tr>
				
			{{Form::close()}}

		</tbody>
	</table>

	<br>
	
	@include('site.partials.form-errors')

</section>
@stop

@extends('admin.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Admin | Tests
@stop

@section('scripts')

@stop




{{-- Content --}}
@section('content')

<?php 
$users = User::take(20)->get();
$tasks = Task\Task::take(20)->get();
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
	
			{{Form::open(['url'=>'admin/tests/send', 'method'=>'POST'])}}
				
					<tr>
				      	<td>
				      		<select class="ui dropdown" name="event">
				      		@foreach (Notification::$eventTypes as $event)
				      			<option value="{{$event}}"><code>{{$event}}</code></option>
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

    				<tr class="center aligned">
		    			<td colspan="4">
		    				<div class="ui action input">
								<input type="text" placeholder="Where to send email..." name="emails" value="{{Auth::user()->email}}">
								<button type="submit" class="ui button">Send</button>

							</div>
							<div><small><b>comma separated list</b></small></div>
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
	
			{{Form::open(['url'=>'admin/tests/view-email', 'method'=>'GET'])}}
				
					<tr>
				      	<td>
				      		<select class="ui dropdown" name="event">
				      		@foreach (Notification::$eventTypes as $event)
				      			<option value="{{$event}}"><code>{{$event}}</code></option>
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

    				<tr class="center aligned">
		    			<td colspan="4">
							<button type="submit" class="ui button">View Email</button>
					    </td>
		    		</tr>
				
			{{Form::close()}}

		</tbody>
	</table>

	<br>
	
	@include('site.partials.form-errors')

</section>
@stop

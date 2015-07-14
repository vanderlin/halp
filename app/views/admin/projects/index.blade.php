@extends('admin.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Admin | Projects
@stop

@section('scripts')

@stop


{{-- Content --}}
@section('content')

<section class="content admin">
	@if ($projects->count()>0)
		<table class="ui celled table">
		<thead>
			<tr>
				<th>#</th>
				<th>Event</th>
				<th>Task</th>
				<th>Sent</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($projects as $project)
				<tr>
					<td>{{$project->id}}</td>
					<td><code>{{$project->title}}</code></td>
				</tr>
			@endforeach
		</tbody>
	</table>	
	<br>
	<div class="progress-button small">
		{{Form::open(['url'=>'notifications/send'])}}
		<button><span>Send Notifications</span></button>
		{{Form::close()}}
	</div>
	
	@include('site.partials.form-errors')

	@else
		<h3>No Notifications</h3>
	@endif

</section>
@stop

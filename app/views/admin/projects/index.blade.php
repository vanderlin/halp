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
			</tr>
		</thead>
		<tbody>
			@foreach ($projects as $project)
				<tr>
					<td>{{$project->id}}</td>
					<td><code>{{$project->title}}</code></td>
					<td><code>{{$project->tasks->count()}}</code></td>
				</tr>
			@endforeach
		</tbody>
	</table>	
	
	@endif

</section>
@stop

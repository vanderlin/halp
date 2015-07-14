@extends('admin.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Admin | Notifications
@stop

@section('scripts')

@stop




{{-- Content --}}
@section('content')

<section class="content admin">
	@if ($notifications->count()>0)
		<table style="width:900px;">
		<thead>
			<tr>
				<td>#</td>
				<td>Event</td>
				<td>Task</td>
				<td>Sent</td>
			</tr>
		</thead>
		<tbody>
			@foreach ($notifications as $notice)
				<tr>
					<td>{{$notice->id}}</td>
					<td>{{$notice->event}}</td>
					<td>{{$notice->task->title}}</td>
					<td>
						@if ($notice->task->sent==NULL)
							<b style="color:red">Not Sent</b>
						@else
							Sent on {{$notice->task->sent->toFormattedDateString()}}
						@endif
					</td>
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

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
			@foreach ($notifications as $notice)
				<tr>
					<td>{{$notice->id}}</td>
					<td><code>{{$notice->event}}</code></td>
					<td>{{link_to($notice->task->getURL(), $notice->task->title)}}</td>
					<td style="text-align:center">
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

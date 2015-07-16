@extends('admin.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Admin | Notifications
@stop

@section('scripts')
<script type="text/javascript">
	$(document).ready(function($) {
		$('.send-notification').click(function(e) {
			e.preventDefault();
			var id = $(this).data('id');			
			var $btn = $(this);
			$btn.addClass('loading');

			
			$.ajax({
				url: '/notifications/send/'+id,
				type: 'POST',
				dataType: 'json',
			})
			.always(function(e) {
				console.log(e);
				if(e.status == 200) 
				{
					$btn.parent().removeClass('negative');
					$btn.parent().html(' <i class="large green checkmark icon"></i>');
				}
			});
			
			
		});		
	});
</script>
@stop


{{-- Content --}}
@section('content')

<section class="content admin">
	@if ($notifications->count()>0)
		<table class="ui celled table notifications">
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
				<tr data-id="{{$notice->id}}">
					<td>{{$notice->id}}</td>
					<td><code>{{$notice->event}}</code></td>
					<td>
						<div class="content">
						<b>{{link_to($notice->task->getURL(), $notice->task->title)}}</b>
						<small>Created by: {{link_to($notice->task->creator->getProfileURL(), $notice->task->creator->getName())}}</small>
						
						@if ($notice->event == Notification\Notification::NOTIFICATION_TASK_CLAIMED)
							<div class="sub header">
								<small>Claimed by: {{link_to($notice->task->claimer->getProfileURL(), $notice->task->claimer->getName())}}</small>
							</div>
						@endif
						</div>
					</td>
					<td class="center aligned {{$notice->task->sent==NULL?'negative':''}}">
						@if ($notice->task->sent==NULL)
							{{-- <b class="status">Not Sent</b> --}}
							<a data-id="{{$notice->id}}" class="send-notification ui mini button" href="#send">Send</a>
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

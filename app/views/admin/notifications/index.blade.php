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
				<th>Task</th>
				<th>Event</th>
				
				<th>Sent</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($notifications as $notice)
				<tr data-id="{{$notice->id}}">
					<td class="center aligned">{{$notice->id}}</td>
					<td>
						<div class="ui list">
							<div class="item">
								@if ($notice->contextUser())
									<img class="ui avatar image" src="{{$notice->contextUser()->profileImage->url('s30')}}">	
								@else
									<small>Missing User</small>
								@endif
								
							<div class="content">
							<a class="header">
								{{link_to($notice->task->getURL(), $notice->task->title)}}								
							</a>
							<div class="description">
								<small>{{$notice->created_at->diffForHumans()}}</small>
								@if ($notice->event == Notification::NOTIFICATION_TASK_CLAIMED)
									<div class="sub header">
										<small>Claimed by: {{link_to($notice->task->claimer->getProfileURL(), $notice->task->claimer->getName())}}</small>
									</div>
								@elseif ($notice->event == Notification::NOTIFICATION_TASK_DELETED)
									<div class="sub header">
										<small>Claimed by: {{link_to($notice->task->claimer->getProfileURL(), $notice->task->claimer->getName())}}</small>
									</div>
								@endif
							</div>
							</div>
							</div>
						</div>
						
					</td>
					
					<td><code>{{$notice->event}}</code></td>

					<td class="center aligned {{$notice->isSent?'positive':'negative'}}">
						@if ($notice->isSent)
							Sent {{$notice->sent_at->diffForHumans()}}
							<div><small><a data-id="{{$notice->id}}" class="send-notification" href="#re-send">Re-Send</a></small></div>
						@else
							<a data-id="{{$notice->id}}" class="send-notification ui mini button" href="#send">Send</a>
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

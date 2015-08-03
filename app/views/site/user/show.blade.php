@extends('site.layouts.default', ['use_navigation'=>true, 'use_footer'=>true])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}} | {{$user->getName()}}
@stop

@section('head')
@stop

@section('scripts')
<script type="text/javascript">
	jQuery(document).ready(function($) {
	
		$('.notifications-setting-form button[type="submit"]').hover(function() {
			var val = $('.notifications-setting-form input[name="notifications"]').val();
			var notifications = val==0?'/assets/img/x-hover.svg':'/assets/img/check-mark-hover.svg';
			$(this).find('img').attr('src', notifications);
			$('.notifications-message').html(val==1?'Enable E-mails':'Disable E-mails');
		}, function() {
			var val = $('.notifications-setting-form input[name="notifications"]').val();
			var notifications = val==1?'/assets/img/x.svg':'/assets/img/check-mark.svg';
			$(this).find('img').attr('src', notifications);
			$('.notifications-message').html(val==0?'E-mails Are Enabled':'E-mails Are Disabled');
		});
	});
	$(function() {
		$(".meter > span").each(function() {
			$(this)
				.data("origWidth", $(this).width())
				.width(0)
				.animate({
					width: $(this).data("origWidth")
				}, 1200);
		});
	});
</script>
@stop


@section('content')
		
	<section class="hero bgcolor2">
		@if (Auth::check() && Auth::id() == $user->id)
			<h3 class="light-h3">{{$user->getName()}}</h3>
			@if($user->hasRole('Admin'))
				<small>{{link_to('admin', 'Admin')}}</small>
			@endif
			<hr>
			<p>E-mail me when a new task is added to Halp:</p>
			{{Form::open(['route'=>['user.update', Auth::id()], 'class'=>'notifications-setting-form'])}}
			<input type="hidden" name="notifications" value="{{$user->notifications?0:1}}">
			<button type="submit"><img class="check-box" src="{{img($user->notifications?'check-mark.svg':'x.svg')}}"></button>
			{{Form::close()}}
			<h5 class="notifications-message">{{$user->notifications?'E-mails Are Enabled':'E-mails Are Disabled'}}</h5>
		@else
			<div class="user-overview">
				<img src="{{$user->profileImage->url('s280')}}">
			</div>
			<h3 class="light-h3">{{$user->getName()}}</h3>
		@endif
	</section>

	<section class="user-task-awards">
		<div class="user-award filled">
			<img src="{{asset('assets/img/user-award-5.svg')}}">
			<p class="progress-title">5 Tasks Claimed</p>
			<h4>5/4/15</h4>
		</div>
	</section>

	<section class="task-ratio">
		<p class="progress-title">Tasks Claimed vs. Tasks Created</p>
		<h4>{{$user->totalClaimed()}} Claimed <span class="slash">/</span> {{$user->totalCreated()}} Created</h4>
		<div class="tug-of-war">
			<img src="{{asset('assets/img/progress-turtle.svg')}}" data-no-retina class="tug-of-war-turtle">
			<img src="{{asset('assets/img/progress-skunk.svg')}}" data-no-retina class="tug-of-war-skunk">
			<div class="meter"> 
				<span style="width: {{$user->taskRatio*100}}%"></span>
			</div>
		</div>
	</section>

	<section class="tasks user-tasks claimed-task-section">
		<div class="line-break">
			<h2>{{$user->totalClaimed()}} Claimed Task{{$user->totalClaimed()>1?'s':''}}!</h2>
			@if (Auth::check() && Auth::id() == $user->id)
				<h6>Pro tip: roll over a task to return it.</h6>
			@else
				<h6>Pro tip: Roll over a task to claim it</h6>
			@endif
			
		</div>
		@forelse ($user->claimedTasks as $task)
			@include('site.tasks.claimed-task', array('task' => $task))
		@empty
			<br/><h5>It's time to start helping.</h5>
		@endforelse
	</section>

	<section class="tasks user-tasks">
		<div class="line-break">
			<h2>{{$user->totalCreated()}} Task{{$user->totalCreated()>1?'s':''}} Created!</h2>
		</div>
		@forelse ($user->createdTasks as $task)
			@include('site.tasks.card', array('task' => $task, 'class'=>'user-created-card', 'show_button'=>$task->isMine()?true:($task->isClaimed?false:true)))
		@empty
			<br/><h5>Go make some {{link_to('/', 'tasks')}}</h5>
		@endforelse
	</section>
@stop
    

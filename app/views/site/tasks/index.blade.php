@extends('site.layouts.default', ['use_navigation'=>true, 'use_footer'=>true])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}}
@stop

@section('head')
@stop

@section('scripts')
<script type="text/javascript">
	$(document).ready(function($) {

		// task-claim-popup
		var params = getQueryParams();
		
		if(params.claim_task !== undefined)
		{
            App.openPopup({
                url:'/tasks/'+params.claim_task+'/claimed'
            });
			$('.task-card-'+params.claim_task).addClass('task-focused')
		}
		
		@if(Auth::user()->isAdmin())
		if(params.edit_task!==undefined) {
			App.editTask(params.edit_task);
		}
		if(params.title!==undefined&&params.project!==undefined&&params.duration!==undefined)
		{	
			var $form = $('#init-create-task');
			$form.find('input[name="title"]').val(params.title);
    		$form.find('input[name="project"]').val(params.project);
    		$form.find('input[name="duration"]').val(params.duration);
			$form.find('.input').addClass('input--filled');

			$('#init-create-task').validateTask();
		}
		@endif

		$('#init-create-task').addValidationListener();

		// -------------------------------------
		$('#init-create-task button[type="submit"]').click(function(e) {
			e.preventDefault();
			var $form = $('#init-create-task');
			var validation = $form.validateTask();
			if(validation.valid)
			{
				$form.openCreateTaskPopup({data:validation.data});
			}
		});	
	});
</script>
@stop


@section('content')
	
	@if (Auth::check() && isset($tasks))
		@include('site.tasks.create-task')
	@endif
		
	<section class="content" id="tasks">
		
		@if (isset($title))
			<h3>Tasks for {{$title}}</h3>
		@endif
		
		@if ($tasks->count()>0)
			@foreach ($tasks as $task)
				@include('site.tasks.card', array('task' => $task))
			@endforeach
			<?php Paginator::setPageName('tasks_page'); ?>
			{{$tasks->appends('tasks_page', Input::get('tasks_page', 1))->fragment('tasks')->links()}}
		@else
			<h3>No Tasks</h3>
		@endif

	</section>

	<div class="turtle-break" id="claimed-task">
		<div class="turtle-line"></div>
		<img src="{{asset('assets/img/happy-turtle.png')}}" width="111px" height="58px" />
		<div class="turtle-line"></div>
		<h3>Claimed Tasks{{isset($title)?' for '.$title:''}}:</h3>
	</div>

	<section class="content" id="claimed-tasks-content">
		@if ($claimed_tasks->count()>0)
			@foreach ($claimed_tasks as $task)
				@include('site.tasks.card', array('task' => $task, 'show_button'=>false))
			@endforeach
			<?php Paginator::setPageName('claimed_tasks_page'); ?>
			{{$claimed_tasks->appends('claimed_tasks_page', Input::get('claimed_tasks_page', 1))->fragment('claimed-task')->links()}}
		@else
			<h3>No Claimed Tasks</h3>
		@endif

	</section>
@stop
  

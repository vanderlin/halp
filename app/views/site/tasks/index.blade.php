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
			App.openClaimPopup(params.claim_task);
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
		
	<section class="content" id="tasks-content">
	@forelse ($tasks as $task)
		@include('site.tasks.card', array('task' => $task, 'claimed'=>false))
	@empty
		<h3>No Tasks</h3>
	@endforelse
	</section>

	<div class="turtle-break">
		<div class="turtle-line"></div>
		<img src="{{asset('assets/img/happy-turtle.png')}}" width="111px" height="58px" />
		<div class="turtle-line"></div>
		<h3>Claimed Tasks:</h3>
	</div>



	<section class="content" id="claimed-tasks-content">
	@forelse ($claimed_tasks as $task)
		@include('site.tasks.card', array('task' => $task, 'claimed'=>true))
	@empty
		<h3>No Claimed Tasks</h3>
	@endforelse
	</section>
@stop
  

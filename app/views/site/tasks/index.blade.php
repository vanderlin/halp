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
		
		if(params.title!==undefined&&params.project!==undefined&&params.duration!==undefined)
		{
			$(this).createTask({
				data:params
			});
		}

		// -------------------------------------
		$('#init-create-task button[type="submit"]').click(function(e) {
			e.preventDefault();
			
			$(this).createTask({
				data:data
			});
			return;
			
			var url = $form.prop('action')+'?view=true';
			var fd = new FormData($form[0]);    

			
			$.ajax({
				url: url,
				data: fd,
  				processData: false,
  				contentType: false,
				type: 'POST',
				dataType: 'json',
			})
			.always(function(e) {
				if(e.status == 400)
				{
					var data = e.responseJSON;
					var $errorcontainer = $form.find('.error-container');
					$errorcontainer.html('');
					var str = '<ul class="alert alert-error alert-danger">';
					for(var i=0; i<data.errors.length; i++){
						str += '<li>'+data.errors[i]+'</li>';
					}
					str += '</ul>';
					var $message = $(str);
					$message.hide().fadeIn(300).delay(3000).slideUp(200);
					$errorcontainer.append($message);
				}
				else if(e.status == 200)
				{
					var $content = $('#tasks-content');
					var $view = $(e.view);
					$content.prepend($view);
					$view.addClass('task-focused');
					$view.hide().fadeIn(300);

					$form.find('.input').removeClass('input--filled');
					$form[0].reset();

					var $delbtn = $view.find('.halp-delete-task-button');
					App.addDeleteTaskEvent($delbtn);
				}
			});
			
		});	
	});
</script>
@stop


@section('content')
	
	<div id="task-claim-popup" class="white-popup mfp-hide">
	</div>

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



	<section class="content">
	@forelse ($claimed_tasks as $task)
		@include('site.tasks.card', array('task' => $task, 'claimed'=>true))
	@empty
		<h3>No Claimed Tasks</h3>
	@endforelse
	</section>
@stop
  

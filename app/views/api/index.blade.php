@extends('site.layouts.default', ['use_navigation'=>true, 'use_footer'=>false])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}} | API
@stop

@section('head')
    <link rel="stylesheet" type="text/css" href="{{bower('semantic-ui/dist/semantic.min.css')}}">
    <script src="{{bower('semantic-ui/dist/semantic.min.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{css('core/api.css')}}">
@stop

@section('scripts')
	<link rel="stylesheet" href="{{js('highlight/styles/default.css')}}">
	<script src="{{js('highlight/highlight.pack.js')}}"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		hljs.configure({
			tabReplace: '  ', // 4 spaces
			// classPrefix: ''
		})
  		$('pre code').each(function(i, block) {
    		hljs.highlightBlock(block);
  		});

  		$('#run-console').click(function(e) {
  			e.preventDefault();
  			var ep = $('#console-form select').val();
  			var param = $('#param-field input').val();
  			param = param!=""?param:20;
  			var url = ep.replace('{id}', param);
  			$.ajax({
  				url: url,
  				type: 'GET',
  				dataType: 'text',
  				data: {pretty:true}
  			})
  			.always(function(e) {
				hljs.fixMarkup(e);
				$('.console-results').html($.trim(e));

				hljs.highlightBlock($('.console-results')[0]);
  			});
  		});

  		$('#param-field').hide();
  		$('#console-form select').change(function(e) {
  			var val = $(this).val();
  			if(val.includes('{id}')) {
  				$('#param-field').fadeIn(200);
  			}
  			else {
  				$('#param-field').fadeOut(200);
  			}
  		});


	});
	</script>
@stop


@section('content')
	
<div class="api-container">

	<section>
				
			<img src="{{img('turtle-lines.jpg')}}">
			<h1 class="api-logo">Halp. API</h1>
			<div class="ui divider"></div>
			@if ($user->set_password == 0 || Input::get('reset_password', false)==true)
				<div class="ui centered grid">
					<div class="six wide tablet eight wide computer column">
					<p id="reset_password">You need to set a password to access the Halp API.</p>

					{{Form::open(['route'=>array('user.update', $user->id), 'class'=>'ui form text-left'])}}
						<div class="field">
							<label>Username</label>
							<input type="text" name="username" placeholder="Username" disabled value="{{$user->username}}">
						</div>
						
						<div class="field">
							<label>Password</label>
							<input type="password" name="password" autocomplete="off" autocapitalize="off">
						</div>
						<div class="field">
							<label>Confirm Password</label>
							<input type="password" name="password_confirmation" autocomplete="off" autocapitalize="off">
						</div>
						
						<button class="ui button" type="submit">Submit</button>
					{{Form::close()}}
					<div class="text-center">
						@include('site.partials.form-errors')
					</div>
					</div>
				</div>
			@else
				<h2 class="ui header">You have access to the Halp. API</h2>
				<p>{{Auth::user()->email}}</p> 
				<a href="/developer/?reset_password=true#reset_password" class="ui button">Reset Password</a> 
			@endif
			
	</section>
	
	<div class="ui divider"></div>

	<section class="text-left">
		
		<div class="ui grid">
      		<div class="eight wide column">
      			<h2>API Apps</h2>
				<table class="ui celled striped table">
					<thead>
				    	<tr>
				    	<th><code>ID</code></th>
				    	<th><code>Project Name</code></th>
				    	<th colspan="3"><code>API Key</code></th>
				    	
				  		</tr>
				  	</thead>
				  	<tbody>
					  	@forelse ($clients as $client)
					  		<tr data-id="{{$client->id}}">
						  		<td>{{$client->id}}</td>
						  	  	<td>{{$client->name}}</td>
							  	<td>{{$client->api_key}}</td>
								<td>
									<button data-id="{{$client->id}}" class="ui button mini">Delete</button>
								</td>
							</tr>
					  	@empty
					  		<tr class="text-center"><td colspan="3"><h4>No Clients Registered</h4></td></tr>
					  	@endforelse
					</tbody>
				</table>
      		</div>

      		<div class="eight wide column">
      			<h2 class="ui header">Create New API Client</h2>
        		{{Form::open(['route'=>array('api.create.client'), 'class'=>'ui form text-left'])}}
					<div class="field">
						<label>Project Name</label>
						<input type="text" name="name" placeholder="ie: my_api_app" value="{{Input::old('name')}}">
					</div>
					<div class="field">
						<label>Project URL</label>
						<input type="text" name="project_url" placeholder="ie: http://halp-app.com" value="{{Input::old('project_url')}}">
					</div>
					<div class="field">
						<label>Description</label>
						<textarea name="description">{{Input::old('description')}}</textarea>
					</div>
					<button class="ui button" type="submit">Submit</button>
				{{Form::close()}}
      		</div>
      		<br>
      	</div>

      	<div class="text-center">
      		@include('site.partials.form-errors')
      	</div>

	</section>

	<div class="ui divider"></div>
	
	<section class="text-left">
		<div class="row">
      		<div class="seven wide column">
        	<h2 class="ui header">Endpoints</h2>
        		<p>All endpoints require basic.auth</p>
      		</div>
      		<br>

			<div class="ui divided selection list">

				@foreach ($endpoints as $ep)
			  		<?php $ep=(object)$ep ?>
				    <a href="#{{$ep->name}}" class="item">
				    	<div class="ui horizontal label">{{$ep->method}}</div>{{$ep->url}}
				    </a>
				@endforeach
				<a href="#console" class="item">
					<div class="ui horizontal label"><i class="lab icon"></i></div>Console
				</a>
			</div>			
			<br>
			<br>	

      		<div class="nine wide column">
      		@foreach ($endpoints as $ep)
      			@include('api.endpoint', ['data'=>$ep])
      		@endforeach
        	</div>	

        	
			<div class="ui container endpoint">
				
				<h2 class="name" id="console"><a href="#console"><i class="icon lab"></i>Console</a></h2>
				
				<div class="text-left">
					<form action="" class="ui form" id="console-form">
						<div class="three fields">
							<div class="field">
								<label>Gender</label>
								<select class="ui dropdown" name="endpoint">
									@foreach ($endpoints as $ep)
				  						<?php $ep=(object)$ep ?>
										<option value="{{$ep->url}}">{{$ep->url}}</option>
									@endforeach
								</select>
							</div>
							<div class="field" id="param-field">
								<label>Param</label>
								<input type="text" name="param" placeholder="parameter {id} ie: 22">
							</div>
						</div>
					</form>
					<pre><code class="console-results code json"></code></pre>
					<a href="#run-console" class="ui button pull-right" id="run-console">Run</a>
				</div> 
			

			</div>


  		</div>
	</section>
</div>
<br>
<br>
@stop
  

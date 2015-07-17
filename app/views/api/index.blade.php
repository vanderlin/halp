@extends('site.layouts.default', ['use_navigation'=>true, 'use_footer'=>false])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}} | API
@stop

@section('head')
	
	
    <link rel="stylesheet" type="text/css" href="{{bower('semantic-ui/dist/semantic.min.css')}}">
    <script src="{{bower('semantic-ui/dist/semantic.min.js')}}"></script>
    <style type="text/css">
	.text-left {
		text-align: left;
	}
	.text-center {
		text-align: center;
	}
    </style>
@stop

@section('scripts')
	<link rel="stylesheet" href="{{js('highlight/styles/default.css')}}">
	<script src="{{js('highlight/highlight.pack.js')}}"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		hljs.configure({
			tabReplace: '  ', // 4 spaces
			classPrefix: ''
		})
  		$('pre code').each(function(i, block) {
    		hljs.highlightBlock(block);
  		});
	});
	</script>
@stop


@section('content')
	
<div class="ui container">

	<section>
			<h1>Halp API</h1>	
			<h3>{{$user->getName()}}</h3>
			<div class="ui divider"></div>
			@if ($user->set_password == 0 || Input::get('reset_password', false)==true)
				<p>You need to set a password to access the Halp API.</p>
				<div class="ui centered grid">
					<div class="six wide tablet eight wide computer column">
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
				<a href="/api/?reset_password=true" class="ui button">Reset Password</a> 
			@endif
			
	</section>

	<div class="ui divider"></div>
	
	<section class="text-left">
		<div class="row">
      		<div class="seven wide column">
        	<h2 class="ui header">Endpoints</h2>
        		<p>Semantic uses simple phrases called behaviors that trigger functionality.</p>
      		</div>
      		<br>
      		<div class="nine wide column">
      		@foreach ($endpoints as $ep)
      			@include('api.endpoint', ['data'=>$ep])
      		@endforeach
        	</div>	
  		</div>
	</section>
</div>

@stop
  

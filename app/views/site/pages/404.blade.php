@extends('site.layouts.default', ['use_navigation'=>false, 'use_footer'=>false])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}} | 404 
@stop

@section('content')
		
	<section>
		<div class="error-404">
			<h1>404</h1>
			<h2>Lost your shell?</h2>
			<h3>The page you're looing for couldn't be found.</h3>
			<div class="progress-button">
				<a href="{{URL::to('/')}}"><button><span>Go Home</span></button></a>
			</div>
			<img src="{{asset('assets/img/naked-turtle.svg')}}">
		</div>
	</section>
@stop
    

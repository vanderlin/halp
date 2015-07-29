@extends('emails.layout', ['css_class'=>'news-letter'])


@section('header')
	<a href="{{production_url()}}">
		<img class="img-responsive" src="{{production_url('assets/emails/welcome-header@2x.png')}}">
	</a>
@stop

@section('content')
	<h1>Welcome to Halp!</h1>
	<hr>

	<p class="purple-text">Thanks for joining! You’re the best.</p>
	
	<a href="{{production_url('login')}}"><div class="rounded-button">Go to Halp</div></a>
@stop
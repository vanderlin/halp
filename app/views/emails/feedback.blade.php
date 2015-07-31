@extends('emails.layout', ['css_class'=>'news-letter', 'hide_unsubscribe'=>true])


@section('header')
	<a href="{{production_url()}}">
		<img class="img-responsive" src="{{production_url('assets/emails/welcome-header@2x.png')}}">
	</a>
@stop

@section('content')
	<h1 class="feedback">Feedback from {{$from->getName()}}</h1>
	<hr>

	<p class="purple-text text-left">{{$feedback}}</p>
@stop
@extends('emails.layout', ['css_class'=>'news-letter', 'hide_unsubscribe'=>true])


@section('header')
	<a href="{{production_url()}}">
		<img class="img-responsive" src="{{production_url('assets/emails/welcome-header@2x.png')}}">
	</a>
@stop

@section('content')
	<img class="img-circle" src="{{production_url($from->profileImage->url('s120'))}}" alt="{{$from->getName()}}">
	<h2 class="feedback">Feedback from {{link_to($from->getProfileURL(), $from->getName())}}</h2>
	<hr>

	<p class="purple-text text-left">{{$feedback}}</p>
@stop
@extends('emails.layout', ['css_class'=>'award news-letter'])

@section('content')
	<h1>{{Award::isOnce($award->name) ? 'You’re a winner!' : 'You passed a major milestone. Celebrate!'}}</h1>
	<img src="{{asset($award->image)}}">
	{{$award->getEmailMessage()}}
	<a href="{{production_url('login')}}"><div class="rounded-button">See The Award</div></a>
@stop
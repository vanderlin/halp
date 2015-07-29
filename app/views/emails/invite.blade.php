@extends('emails.layout', ['css_class'=>'news-letter'])


@section('header')
	<a href="{{production_url()}}">
		<img class="img-responsive" src="{{production_url('assets/emails/invite-header@2x.png')}}">
	</a>
@stop

@section('content')
		
	
	<h1>You’re invited to Halp!</h1>
	<hr>

	<p class="purple-text">
	<a href="{{production_url()}}">Halp</a> is a task board for our studio. It connects willing volunteers to folks who need help with anything from proofreading a deck to moving a couch. 
	</p>
	
	<p>
		<a href="{{production_url('login')}}">
			<img src="{{production_url('assets/emails/task-invite.png?'.uniqid())}}">
		</a>
	</p>
	
	<p class="purple-text">If you're between projects, it's a great place to find small-scale tasks to experiment with new skills. To get started, log in with your IDEO e-mail credentials, and Halp will notify you whenever a new task is created.</p>
	
	<a href="{{production_url('login')}}"><div class="rounded-button">Log in to Halp</div></a>

@stop
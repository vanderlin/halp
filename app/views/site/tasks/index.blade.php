@extends('site.layouts.default', ['use_navigation'=>true, 'use_footer'=>false])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}}
@stop

@section('head')
@stop

@section('scripts')
@stop


@section('content')

	@if (Auth::check() && isset($tasks))
		@include('site.partials.create-task')
	@endif
		
		
		<section class="content">
		@forelse ($tasks as $task)
			@include('site.tasks.card', array('task' => $task))
		@empty
			<h3>No Tasks</h3>
		@endforelse
		</section>
@stop
    
{{--


<h1>HALP</h1>
@if (Auth::check())
	<ul>
		<li>
			{{ link_to(Auth::user()->getProfileURL(), 'view profile')}}
		</li>

		<li>
			{{ link_to('logout', 'Sign Out')}}
		</li>
	</ul>
@endif
--}}
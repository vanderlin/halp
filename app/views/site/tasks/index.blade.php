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
	
	<div id="test-popup" class="white-popup mfp-hide">
  		Popup content
	</div>

	@if (Auth::check() && isset($tasks))
		@include('site.partials.create-task')
	@endif
		
	<section class="content">
	@forelse ($tasks as $task)
		@include('site.tasks.card', array('task' => $task, 'claimed'=>false))
	@empty
		<h3>No Claimed Tasks</h3>
	@endforelse
	</section>

	<section class="content">
	@forelse ($claimed_tasks as $task)
		@include('site.tasks.card', array('task' => $task, 'claimed'=>true))
	@empty
		<h3>No Claimed Tasks</h3>
	@endforelse
	</section>
@stop
  

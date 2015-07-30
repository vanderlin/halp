@extends('admin.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Admin | Tasks
@stop

@section('scripts')

@stop


{{-- Content --}}
@section('content')

<section class="content admin">
	
	<div class="center aligned" colspan="3">Today: {{Carbon\Carbon::now()}}</div>
	<div class="center aligned" colspan="3">
		<ul>
			<li>Expired Tasks: {{$expired_tasks->count()}}</li>
			<li>Active Tasks: {{$active_tasks->count()}}</li>
			<li>Total Tasks: {{$tasks->count()}}</li>
		</ul>
	</div>
	{{-- @include('admin.tasks.table', array('tasks' => $tasks))	   --}}
	
	<div class="ui top attached tabular menu">
	  <a class="item active" data-tab="active-tasks">Active Tasks ({{$active_tasks->count()}})</a>
	  <a class="item" data-tab="expired-tasks">Expired Tasks ({{$expired_tasks->count()}})</a>
	  <a class="item" data-tab="tasks">All Tasks ({{$tasks->count()}})</a>
	</div>
	
	
	<div class="ui bottom attached tab segment active" data-tab="active-tasks">
	  @include('admin.tasks.table', array('tasks' => $active_tasks))	  
	</div>
	<div class="ui bottom attached tab segment" data-tab="expired-tasks">
		@include('admin.tasks.table', array('tasks' => $expired_tasks))	  
	</div>
	<div class="ui bottom attached tab segment" data-tab="tasks">
		@include('admin.tasks.table', array('tasks' => $tasks))	  
	</div>
	<br><br><br>
</section>
@stop

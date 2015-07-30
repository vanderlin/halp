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
	
	<div class="ui top attached tabular menu">
	  <a class="item active" data-tab="active-tasks">Active Tasks</a>
	  <a class="item" data-tab="expired-tasks">Expired Tasks</a>
	</div>
	
	<div class="ui bottom attached tab segment active" data-tab="active-tasks">
	  @include('admin.tasks.table', array('tasks' => $active_tasks))	  
	</div>
	<div class="ui bottom attached tab segment" data-tab="expired-tasks">
		@include('admin.tasks.table', array('tasks' => $expired_tasks))	  
	</div>
	
</section>
@stop

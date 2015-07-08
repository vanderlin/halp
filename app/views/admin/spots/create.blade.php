@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | Add a spot
@stop


@section('scripts')
@include('admin.spots.add-spot-js')
@stop

{{-- Content --}}
@section('content')
	
<h2 class="page-header">Add a spot</h2>
	
<div class="col-md-12">
  	<div class="row">

		
		<div class="panel panel-default">
			
			<div class="panel-body">
				@include('admin.spots.add-spot-form')
			</div>

			
		</div>

	</div>
</div>

@stop


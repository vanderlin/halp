@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | {{$spot->name}}
@stop


@section('scripts')
	<script type="text/javascript">
		var spotLocation = {};
		@if (isset($spot) && isset($spot->location))
			spotLocation.place_id = "{{$spot->location->place_id}}";
			spotLocation.lat = {{$spot->location->lat}};
			spotLocation.lng = {{$spot->location->lng}};
			spotLocation.name = "{{$spot->name}}";
		@endif
	</script>
	@include('admin.spots.add-spot-js')
@stop

{{-- Content --}}
@section('content')
	
<div class="page-header">
    <h2 class="inline">
      {{ $spot->name }}
    </h2>
    <h5 class="inline">
    	<a href="{{$spot->getURL()}}">view</a> | 
    	{{ link_to('admin/spots', 'Back to spots') }}
    </h5>
</div>
	
<div class="col-md-12">
  	<div class="row">
  		@include('admin.spots.add-spot-form', ['spot'=>$spot])
	</div>
</div>


@stop



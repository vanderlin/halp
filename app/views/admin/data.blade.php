@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | Data	
@stop


@section('scripts')

@stop

{{-- Content --}}
@section('content')
	
	<div class="page-header">
		<h2>Locals Only Data</h2>
		<ul class="list-group">
		@foreach ($data as $key => $element)
			<li class="list-group-item">
			
			@if (is_array($element)===false)
			<h4>
			{{$key}}: {{$element}}
			</h4>

			@endif

			@if (is_array($element))
			<h4>{{$key}}</h4>
			Total: <b>{{$element['total']}}</b>
			@endif
			</li>
		@endforeach	
		</ul>
	</div>
@stop


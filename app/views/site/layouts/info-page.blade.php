@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | {{$title}}
@stop

{{-- Content --}}
@section('content')
	
	<div class="content-container container blog-content-container" style="min-height: 1800px">
		
		<div class="row">
			<div class="col-md-offset-1 col-md-10">
				<h2 class="content-title">{{$title}}</h2>
				<p>{{$message}}</p>	
				{{link_to(URL::to('/'), 'Home')}}
			</div>		    
	    </div>

	</div>
	




@stop

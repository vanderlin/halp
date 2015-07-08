
@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  {{Config::get('config.site_name')}} | {{$role->name}}
@stop

{{-- Content --}}
@section('content')



  <div class="page-header">
  	{{ $role->name }}
  	<a href="{{URL::to('/admin/roles')}}">Back</a>
  </div>
  
  <div class="well">

	{{Form::open([	'url'=>'admin/roles/'.$role->id, 
	                'method'=>'PUT', 
                    'role'=>"form"])}}

	    <div class="row">
	        <div class="col-sm-6">
	          	<!-- Name -->
				<div class="form-group">
					<label for="name">Name</label>
					<input type="text" class="form-control" id="name" placeholder="ie: Editor" name="name" value="{{$role->name}}">
				</div>

		        <!-- Display Name -->
				<div class="form-group">
					<label for="name">Display Name</label>
					<input type="text" class="form-control" id="display_name" placeholder="ie: Rock Star!" name="display_name" value="{{$role->display_name}}">
				</div>
			</div>
	    </div>

	      
		<div class="form-group">
			<div class="">
				<button type="submit" class="btn btn-default">Update</button>
			</div>
		</div>

    {{Form::close()}}

    <div class="form-group text-center">
      @include('site.partials.form-errors')
    <div>


  </div>

@stop

@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | Blog 
@stop


@section('scripts')

@stop

{{-- Content --}}
@section('content')
  

  <h2 class="page-header">Post Types</h2>

  <div class="row">
		<div class="col-md-8">
			{{ Form::open(['url'=>'admin/blog/types', 'method'=>'POST']) }}
		  	<div class="form-group">
		  		<input class="form-control" placeholder="Name" name="name">
		  	</div>

		  	<div class="form-group">
		  		<button class="btn btn-defaut">Submit</button>
		  	</div>
		  	{{ Form::close() }}
		</div>
  </div>
	
<div class="row">
	<div class="col-md-8">
		<div class="table">
		    <table class="table table-striped">
		    
		      <thead>
		        <tr>
		          <th class="table-id-col">#</th>
		          <th>Name</th>
		          <th></th>
		        </tr>
		      </thead>

		      <tbody>
				@foreach ($postTypes as $type)
		         
		          <tr>
		            <td>{{ $type->id }}</td>
		            <td>
		            	<a href="#" class="edit-in-place" data-type="text" data-pk="{{$type->id}}" data-url="/admin/blog/types/{{$type->id}}" data-method="PUT" data-name="name" data-title="Type Name">{{$type->name}}</a>
		            </td>
		        	<td>{{-- link_to('admin/types/'.$type->id.'/edit', 'Edit', ['class'=>'btn btn-default btn-xs pull-right']) --}}</td>
		          </tr>

		        @endforeach
		      </tbody>
		    </table>
	  	</div>
	</div>
</div>


  <div class="row">
  	<div class="col-md-12 text-center">
		@include('site.partials.form-errors')
	</div>
  </div>

@stop

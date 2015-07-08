@extends('admin.layouts.default')
<?php $tags = Tag::all() ?>

{{-- Web site Title --}}
@section('title')
  Admin | Tags 
@stop


@section('scripts')

@stop

{{-- Content --}}
@section('content')
  
  <h2 class="page-header">Tags</h2>

  <div class="row">
	<div class="col-md-6">
		
		<div class="well">
	      {{Form::open(['url'=>'/admin/tags', 'method'=>'POST', 'id'=>'create-form'])}}
	      
	       	<input type="hidden" value="{{Auth::user()->id}}" name="user_id">

	        <!-- Name -->
	        <div class="form-group">
	          	<label for="name">Name</label>
	        	<input id="name" name="name" autocomplete="off" class="form-control" value="{{Input::old('name')}}">
	        </div>

	        <!-- update -->
	      	<div class="form-group">
	        	<button type="submit" form="create-form" class="btn btn-default">Add</button>
	      	</div>
	      	{{Form::close()}}

	      	<div class="text-center">
	        	@include('site.partials.form-errors')
	    	</div>
  		</div>
  	

  	@if ($tags->count()==0)
  		<div class="text-center">
  			<small>No Tags</small>
  		</div>
  	@else
	    <table class="table table-striped">
	    
	    	<thead>
		        <tr>
		          <th>#</th>
		          <th>Name</th>
		          <th></th>
		        </tr>
	    	</thead>

	      	<tbody>
		        @foreach (Tag::all() as $tag)
		       		<tr>
			            <td>{{ $tag->id }}</td>
			            <td>{{ link_to($tag->getURL(), $tag->name)}}</td>
		             	<td>{{ link_to('admin/tags/'.$tag->id, 'Edit', ['class'=>'pull-right btn btn-xs, btn-default'])}}</td>
		          	</tr>
		        @endforeach
	      	</tbody>
	    </table>
    @endif

  </div>
</div>
@stop

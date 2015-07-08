@extends('admin.layouts.default')
<?php $tags = Tag::all() ?>

{{-- Web site Title --}}
@section('title')
  Admin | Tags 
@stop


@section('scripts')
<script type="text/javascript">
	$(document).ready(function($) {
		$("#delete-tag").click(function(e) {
			e.preventDefault();
			var c = confirm('Are you sure?');
			if(c) {
				$.ajax({
					url: '/admin/tags/{{$tag->id}}',
					type: 'POST',
					dataType: 'json',
					data: {_method: 'DELETE'},
				})
				.done(function(evt) {
					console.log("success", evt);
					if(evt.status == 200) {
						document.location = '/admin/tags';
					}
				})
				.fail(function(evt) {
					console.log("error", evt);
				});
			}
		});				
	});
</script>
@stop

{{-- Content --}}
@section('content')
  
  <h2 class="page-header">{{$tag->name}}</h2>

  <div class="row">
	<div class="col-md-6">
		
		<div class="well">
	      {{Form::open(['url'=>'/admin/tags/'.$tag->id, 'method'=>'PUT', 'id'=>'create-form'])}}
	      
	       	<input type="hidden" value="{{Auth::user()->id}}" name="user_id">

	        <!-- Name -->
	        <div class="form-group">
	          	<label for="name">Name</label>
	        	<input id="name" name="name" autocomplete="off" class="form-control" value="{{$tag->name}}">
	        </div>

	        <!-- update -->
	      	<div class="form-group">
	        	<button type="submit" form="create-form" class="btn btn-default">Update</button>
	        	<a href="#delete" id="delete-tag" class="btn btn-default btn-danger pull-right">Delete</a>
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

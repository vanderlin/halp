@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | Asset 
@stop


@section('scripts')
<script type="text/javascript">
	$(document).ready(function($) {
		
		$("#delete-asset").click(function(e) {
			e.preventDefault();
			var c = confirm('Are you sure?');
			if(c) {
				$.ajax({
					url: '/assets/{{$asset->id}}',
					type: 'POST',
					dataType: 'json',
					data: {_method: 'DELETE'},
				})
				.done(function(evt) {
					console.log("success", evt);
					if(evt.status == 200) {
						document.location = '/admin/assets';
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
  
  

<div class="page-header">
    <h2 class="inline">
  	{{$asset->id}}:{{$asset->filename}}
    </h2>
    <h5 class="inline">
    	{{ link_to('admin/assets', 'Back to Assets') }}
	</h5>
</div>

  <div class="row">
	<div class="col-md-6">
		
		<div class="well">
	      {{Form::open(['url'=>'/assets/'.$asset->id, 'method'=>'PUT', 'id'=>'create-form', 'files'=>true])}}
	      
	       	<input type="hidden" value="{{Auth::user()->id}}" name="user_id">

	        <!-- Name -->
	        <div class="form-group">
	          	<label for="name">Name</label>
	        	<input id="name" name="name" autocomplete="off" placeholder="optional" class="form-control" value="{{$asset->name}}">
	        </div>

	        <!-- FileName -->
	        <div class="form-group">
	          	<label for="filename">Filename</label>
	        	<input disabled name="filename" class="form-control" value="{{$asset->filename}}">
	        </div>

	        <!-- Path -->
	        <div class="form-group">
	          	<label for="path">Path</label>
	        	<input disabled name="path" class="form-control" value="{{$asset->path}}">
	        </div>

          	<!-- tag -->
	        <div class="form-group">
	          	<label for="tag">Tag</label>
	        	<input name="tag" class="form-control" value="{{$asset->tag}}">
	        </div>


	        <!-- file -->
	        <div class="form-group">
	          	
	          	<img src="{{$asset->url('w100')}}"><br>
	          	<input type="file" name="file" id="file-input" accept="image/*" multiple>	
	        </div>


	         <!-- shared -->
	        <div class="form-group">
	        	<div class="checkbox">
					<label>
			    		<input type="checkbox" name="shared" {{(isset($asset->shared)&&$asset->shared==1)?'checked':''}}> Shared
			    	</label>
			  	</div>
	        </div>

	         <!-- Info -->
	        <div class="form-group">
	          	<label for="path">File Exists: {{$asset->fileExists()?'True':'False'}}</label>
	          	<br>
	          	@foreach ($asset->getMissingReleationship() as $missing)
	          		<b>{{$missing[0]}}:</b> {{$missing[1]}} - {{$missing[2]}}<br>
	          	@endforeach
	        </div>


	        <!-- update -->
	      	<div class="form-group">
	        	<button type="submit" form="create-form" class="btn btn-default">Update</button>
	        	<a href="#delete" id="delete-asset" class="btn btn-default btn-danger pull-right">Delete</a>
	      	</div>
	      	{{Form::close()}}

	      	<div class="text-center">
	        	@include('site.partials.form-errors')
	    	</div>
  		</div>
  	
  </div>
</div>
@stop

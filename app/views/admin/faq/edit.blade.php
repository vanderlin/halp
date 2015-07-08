
@extends('admin.layouts.default')


{{-- Web site Title --}}
@section('title')
  {{Config::get('config.site_name')}} | FAQ
@stop



{{-- Content --}}
@section('content')



<h2 class="page-header">
	{{ $faq->question }}
</h2>
  
<div class="row">

		<div class="col-md-6">
			  	{{Form::open(['url'=>$faq->getEditURL(), 'method'=>'PUT', 'id'=>'edit-form'])}}
					

					   <!-- Question -->
				       <div class="form-group">
				          <label for="question">Question</label>
				          <input id="question" name="question" class="form-control" value="{{$faq->question}}">
				        </div>


				        <!-- Question -->
				        <div class="form-group">
				          <label for="answer">Answer</label>
				          <textarea id="answer" name="answer" class="form-control" rows="5">{{$faq->answer}}</textarea>
				          <span class="help-block">This can be HTML text</span>
				        </div>

			    	<!-- update -->
			        <div class="form-group">
		          		<button type="submit" form="edit-form" class="btn btn-default">{{isset($faq) ? 'Update': 'Add' }}</button>
			        </div>
			    {{Form::close()}}

				    @if (isset($faq))
			  		{{Form::open(['url'=>$faq->getEditURL(), 'method'=>'DELETE', 'id'=>'delete-form'])}}
			  			<button type="submit" form="delete-form" class="btn btn-danger">Delete</button>
			  		{{Form::close()}}
			  		@endif
					

			    <div class="text-center">
			    	@include('site.partials.form-errors')
			    </div>
		</div>
</div>


@stop

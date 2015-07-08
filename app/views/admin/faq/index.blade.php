@extends('admin.layouts.default')


{{-- Web site Title --}}
@section('title')
  Admin | FAQ 
@stop


@section('scripts')

@stop

{{-- Content --}}
@section('content')
  
  <h2 class="page-header">FAQs</h2>

  <div class="row">
	<div class="col-md-6">
		<div class="well">
	      {{Form::open(['url'=>'/admin/faqs', 'method'=>'POST', 'id'=>'create-form'])}}
	      
	       	<input type="hidden" value="{{Auth::user()->id}}" name="user_id">

	        <!-- Question -->
	        <div class="form-group">
	          <label for="question">Question</label>
	          <input id="question" name="question" class="form-control" value="{{Input::old('question')}}">
	        </div>


	        <!-- Question -->
	        <div class="form-group">
	          <label for="answer">Answer</label>
	          <textarea id="answer" name="answer" class="form-control" rows="5">{{Input::old('answer')}}</textarea>
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
  </div>



  <div class="col-md-6">
    <table class="table table-striped">
    
      <thead>
        <tr>
          <th>#</th>
          <th>Question</th>
          <th></th>
        </tr>
      </thead>

      
      <tbody>
        @foreach (FAQ::all() as $faq)
       	
       		<tr>
	            <td>{{ $faq->id }}</td>
	            <td>{{ link_to($faq->getURL(), $faq->question)}}</td>
             	<td>{{ link_to('admin/faqs/'.$faq->id, 'Edit', ['class'=>'pull-right btn btn-xs, btn-default'])}}</td>
          	</tr>
          

        @endforeach
      </tbody>
    </table>
  </div>
</div>
@stop

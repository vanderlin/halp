@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | Blog 
@stop


@section('scripts')
<script type="text/javascript">
	$(document).ready(function($) {
		$(".delete-blog-item").click(function(event) {
			event.preventDefault();
			
			var id = $(this).data('id');
			$('.posts-table tr[data-id="'+id+'"]').addClass('danger');

			if(confirm('Are you sure?')) {
			
				$.ajax({
					url: '/admin/blog/post/'+id,
					type: 'POST',
					dataType: 'json',
					data: {_method: 'DELETE'},
				})
				.done(function(e) {

					$('.posts-table tr[data-id="'+e.post_id+'"]').addClass('danger').fadeOut(300, function() {
						$(this).remove();
					})
				})
				.fail(function(e) {
					console.log("error", e);
				})	
			}
			else {
				$('.posts-table tr[data-id="'+id+'"]').removeClass('danger');
			}

		});	
	});
</script>
@stop

{{-- Content --}}
@section('content')
  


  <h2 class="page-header">Blog &amp; Pages</h2>

  <div class="row">
		<div class="col-md-8">
			@foreach ($posts as $row)

				<h4>{{ str_plural($row->name) }}</h4>
				<div role="tabpanel">

					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<?php $first = true; ?>
						@foreach ($row->posts as $key=>$posts)
							<li role="presentation" class="{{$first?'active':''}}"><a href="#{{Str::slug($row->name.'-'.$key)}}" aria-controls="home" role="tab" data-toggle="tab">{{ $key }} <small>({{$posts->count()}})</small></a></li>
							<?php $first = false; ?>
						@endforeach		
					</ul>

					<!-- Tab panes -->
					<div class="tab-content">
						<?php $first = true; ?>
						@foreach ($row->posts as $key=>$posts)
							<div role="tabpanel" class="tab-pane {{$first?'active':''}}" id="{{Str::slug($row->name.'-'.$key)}}">
								
								@if ($posts->count()==0)
									<div class="text-center"><small class="text-muted">No Posts</small></div>
								@else
									<table class="table table-striped posts-table">
									<thead>
										<tr>
											<th>#</th>
											<th>Name</th>
											<th>Date</th>
											<th></th>
										</tr>
									</thead>

									<tbody>
										@foreach ($posts as $post)
											<tr data-id="{{$post->id}}">
												<td>{{ $post->id }}</td>
												<td>{{ $post->title }}</td>
												<td>{{ $post->created_at->format('M d, Y') }}</td>
												<td>
									            	
									            	<div class="pull-right">
									            		{{ link_to('admin/blog/post/'.$post->id, 'Edit', ['class'=>'btn btn-default btn-xs'])}}
									            		<a href="#delete-blog-item" class="btn btn-default btn-xs delete-blog-item" data-id="{{$post->id}}"><span class="glyphicon glyphicon-trash"></span></a>
									            	</div>
									            </td>
											</tr>
										@endforeach
									</tbody>
									</table>			
								@endif

							</div>
						<?php $first = false; ?>
						@endforeach		
					</div>

				</div>

				{{--
				@foreach ($row->posts as $key=>$posts)
					<h5>{{ $key }}</h5>


					@if ($posts->count()==0)
						<div class="text-center"><small class="text-muted">No Posts</small></div>
					@else
						<table class="table table-striped posts-table">
						<thead>
							<tr>
								<th>#</th>
								<th>Name</th>
								<th>Date</th>
								<th></th>
							</tr>
						</thead>

						<tbody>
							@foreach ($posts as $post)
								<tr data-id="{{$post->id}}">
									<td>{{ $post->id }}</td>
									<td>{{ $post->title }}</td>
									<td>{{ $post->created_at->format('M d, Y') }}</td>
									<td>
						            	
						            	<div class="pull-right">
						            		{{ link_to('admin/blog/post/'.$post->id, 'Edit', ['class'=>'btn btn-default btn-xs'])}}
						            		<a href="#delete-blog-item" class="btn btn-default btn-xs delete-blog-item" data-id="{{$post->id}}"><span class="glyphicon glyphicon-trash"></span></a>
						            	</div>
						            </td>
								</tr>
							@endforeach
						</tbody>
						</table>
					@endif
				@endforeach
				--}}
			@endforeach
			
		</div>
  </div>
@stop

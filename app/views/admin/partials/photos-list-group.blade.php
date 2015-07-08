


<script type="text/javascript">

	$(document).ready(function($) {
		
		$(document).on('click', '.delete-photo-btn', function(me) {
			
			var id = $(this).attr('data-id');
			var token = $(this).attr('data-token');

			$.ajax({
				url: '/assets/'+id,
				type: 'POST',
				dataType:'json',
				data: {_method: 'delete', _token :token},
			})
			.done(function(e) {
				if(e.status == 200) {
					var id = e.id;
					$('.list-group-item[data-id="'+id+'"]').fadeOut(200, function() {
						$(this).remove();
					});

					if($(".total-photos").length) {
						if(e.total>0) {
							$(".total-photos").html("Photos ("+e.total+")");	
						}
						else {
							$(".total-photos").html("Photos");	
							$(".photos-list").html('<li class="list-group-item text-center" data-id="-1"><i class="text-muted">No Photos</i></li>');
						}
						
					}
					if(e.total>0) {
						$('.photos-list .list-group-item[data-id="-1"]').remove();	
					}
					

				}
			})
			.fail(function(e) {
				console.log(e);
			});
			
			me.preventDefault();


		});

		

		
	});	


</script>


<ul class="list-group photos-list" {{isset($id)?'id="'.$id.'"':''}}>
	@if ($photos && count($photos)>0)
		
		@foreach ($photos as $photo)
			<li class="list-group-item" data-id="{{$photo->id}}">

				<span>
					<a class="gallery-item" href="{{ $photo->url('w1024') }}" class="image-link">
					<img src="{{ $photo->url('s50') }}">
				</a>
				</span>
				<div class="delete-row pull-right">
					<a data-token="{{ csrf_token() }}" data-id="{{$photo->id}}" class='delete-photo-btn btn btn-danger btn-xs'>delete</a>
				</div>
			</li>
		@endforeach

	@else
		<li class="list-group-item text-center" data-id="-1"><i class="text-muted">No Photos</i></li>
	@endif
</ul>





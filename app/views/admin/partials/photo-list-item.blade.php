<li class="list-group-item asset-item" data-id="{{$image->id}}">
	<div class="media">

		<div class="filelist-left media-middle photo">
			<a href="{{$image->url('w1024')}}" class="lightbox-link">
				<img width="40px" src="{{$image->url('s40')}}">
			</a>
		</div>
		<div class="filelist-right remove-photo media-middle">
			@if(isset($replace)&&$replace == true)
				<a href="#remove-photo" data-id="{{$image->id}}" data-item-id="{{$id}}" data-name="{{$title}}" class="btn btn-default btn-xs {{{$target or ''}}}" data-preview-target="{{{$preview or '#photo-filelist'}}}">replace</a>
				<a href="#delete-photo" data-id="{{$image->id}}" data-name="{{$title}}" data-target="#photos-filelist" class="btn btn-default btn-xs delete-photo-btn "><i class="fa fa-trash-o"></i></a>
    		@else 
    			<a href="#delete-photo" data-id="{{$image->id}}" data-name="{{$title}}" data-target="#photos-filelist" class="btn btn-default btn-xs delete-photo-btn">delete</a>
    		@endif
    	</div>

		<div class="media-body media-middle">
			<div class="file-title hidden-md">{{$title}}</div>
			
			<small class="rights">
			<a href="#" data-value="{{$image->rights}}" data-type="select" data-pk="{{$image->id}}" data-url="/assets/{{$image->id}}" data-name="rights" data-title="Do you own this image?">
			{{$image->isOwnedByUser(Auth::user()) ? '<span class="info text-success">You are the owner of this photo</span>' : '<span class="info text-danger">Set the rights to this photo</span>'}}
			</a>
			</small>
		</div>	

	</div>
</li>
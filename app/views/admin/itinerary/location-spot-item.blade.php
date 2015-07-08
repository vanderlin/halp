<li class="list-group-item" data-id="{{$item->id}}" data-place-id="{{$item->place_id}}">
	<div class="media">
		<div class="media-left">
		<a href="{{ $item->hasSpot() ? $item->spot->getURL() : $item->getURL() }}">
			<img class="media-object" width="80" height="80" src="{{ $item->hasSpot() ? $item->spot->getThumbnail()->url('s80') : $item->getThumbnail()->url('s80') }}">				
		</a>
	</div>

	<div class="media-body">
		<h5 class="media-heading">
		{{ $item->hasSpot() ? $item->spot->name : $item->name }}
		</h5>
		@if (isset($item->added_by))
		<div class="text-muted">
			<small>Added by: {{link_to($item->added_by->getProfileURL(), $item->added_by->getName())}}</small>
		</div>
		@endif
		<small class="text-muted">{{ $item->formatted_address }}</small>
	</div>

	<div class="media-right media-middle">
		<a class="media-object itinerary-remove-location btn btn-danger btn-xs" href="#remove-spot" data-id="{{$item->id}}">Remove</a>	
	</div>
</li>



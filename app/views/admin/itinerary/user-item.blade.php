<div class="list-group-item" data-id="{{$user->id}}">
	<div class="media">
		<div class="media-left">
			<div class="image-badge">
				@include('site.partials.user-image', array('user' => $user, 'size'=>isset($size)?$size:'s40'))
			</div>
		</div>
		<div class="media-body">
			<h5 class="media-heading">{{$user->getName()}}</h5>
			<small class="text-muted">{{ $user->getLocationName() }}</small>
		</div>
		<div class="media-right media-middle">
			<a class="itinerary-remove-user btn btn-danger btn-xs" href="#remove-user" data-id="{{$user->id}}">Remove</a>
		</div>
	</div>
</div>